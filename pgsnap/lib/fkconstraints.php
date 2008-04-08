<?php

/*
 * Copyright (c) 2008 Guillaume Lelarge <guillaume@lelarge.info>
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

$buffer = "<h1>FK constraints list</h1>";


$query = "SELECT
  rolname AS tableowner,
  relname AS tablename,
  conname,
  pg_get_constraintdef(pg_constraint.oid, true) as condef
FROM pg_constraint, pg_class, pg_roles
WHERE conrelid=pg_class.oid
  AND relowner=pg_roles.oid
  AND contype = 'f'
ORDER BY 1, 2, 3";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Table Owner</td>
  <td>Table name</td>
  <td>Constraint name</td>
  <td>Column name</td>
  <td>Referenced Table name</td>
  <td>Referenced Column name</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
//column, referenced table, referenced column
//FOREIGN KEY (id_etat_surveillance) REFERENCES libelle_etat_surveillance(id_etat_surveillance) ON UPDATE RESTRICT ON DELETE RESTRICT
  $pattern = '/FOREIGN KEY \((\w+)\) REFERENCES (\w+)\((\w+)\) (.*)/';
  $replacement = '${1}|${2}|${3}';
  $tmp = preg_replace($pattern, $replacement, $row['condef']);
  $def = split("\|", $tmp);
  $buffer .= "<tr>
  <td>".$row['tableowner']."</td>
  <td>".$row['tablename']."</td>
  <td>".$row['conname']."</td>
  <td>".$def[0]."</td>
  <td>".$def[1]."</td>
  <td>".$def[2]."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fkconstraints.html';
include 'lib/fileoperations.php';

?>
