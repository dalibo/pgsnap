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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Tables without PKEY list</h1>
';

$buffer .= '<label><input id ="showusrobjects" type="checkbox" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" checked>Show System Objects</label>';

$query = "SELECT
  pg_get_userbyid(relowner) AS owner,
  nspname,
  relname,";
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(pg_relation_size(pg_class.oid)) AS size';
} else {
  $query .= '
  relpages*8192 AS size';
}
  $query .= "
FROM pg_class, pg_namespace
WHERE
  relkind='r'
  AND relhaspkey IS false
  AND relnamespace = pg_namespace.oid
ORDER BY relowner, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Table Owner</th>
  <th class="colMid">Schema Name</th>
  <th class="colMid">Table Name</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colLast" width="200">Size</th>';
} else {
  $buffer .= '
  <th class="colLast" width="200">Estimated Size</th>';
}
  $buffer .= '
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td>".$row['owner']."</td>
  <td>".$row['nspname']."</td>
  <td>".$row['relname']."</td>
  <td>".$row['size']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tableswithoutpkey.html';
include 'lib/fileoperations.php';

?>
