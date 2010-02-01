<?php

/*
 * Copyright (c) 2008-2010 Guillaume Lelarge <guillaume@lelarge.info>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>FK Constraints</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  pg_get_userbyid(relowner) AS tableowner,
  nspname,
  relname AS tablename,
  conname,
  pg_get_constraintdef(pg_constraint.oid, true) as condef
FROM pg_constraint, pg_class, pg_namespace
WHERE conrelid=pg_class.oid
  AND relnamespace=pg_namespace.oid";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
  AND contype = 'f'
ORDER BY 1, 2, 3";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Table Owner</th>
  <th class="colMid">Table name</th>
  <th class="colMid">Constraint Name</th>
  <th class="colMid">Column Name</th>
  <th class="colMid">Referenced Table Name</th>
  <th class="colLast">Referenced Column Name</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
//column, referenced table, referenced column
//FOREIGN KEY (id_etat_surveillance) REFERENCES libelle_etat_surveillance(id_etat_surveillance) ON UPDATE RESTRICT ON DELETE RESTRICT
  $pattern = '/FOREIGN KEY \((\w+)\) REFERENCES (\w+)\((\w+)\) (.*)/';
  $replacement = '${1}|${2}|${3}';
  $tmp = preg_replace($pattern, $replacement, $row['condef']);
  $def = split("\|", $tmp);
  if (count($def) == 1) {
    $def[1] = '';
    $def[2] = '';
  } elseif (count($def) == 2) {
    $def[2] = '';
  }
  $buffer .= tr($row['nspname'])."
  <td>".$row['tableowner']."</td>
  <td>".$row['nspname'].".".$row['tablename']."</td>
  <td>".$row['conname']."</td>
  <td>".$def[0]."</td>
  <td>".$def[1]."</td>
  <td>".$def[2]."</td>
</tr>";
// TODO def[1] et def[2] pas forcément défini
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fkconstraints.html';
include 'lib/fileoperations.php';

?>
