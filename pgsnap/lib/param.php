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

$buffer = "<h2>General Configuration</h2>

<p><b>Be careful</b>, this is not the result from reading the postgresql.conf
file, but it's the actual configuration available when connecting to database
".$PGDATABASE." on server ".$PGHOST.":".$PGPORT." as user ".$PGUSER.".</p>";

$query = "SELECT name,
  setting,";
if ($g_version >= 82) {
  $query .= "
  unit,";
}
if ($g_version >= 74) {
  $query .= "
  category,
  short_desc,
  extra_desc,";
}
$query .= "
  context,
  vartype,
  source,
  min_val,
  max_val
FROM pg_settings
ORDER BY ";
if ($g_version >= 74) {
  $query .= "category, ";
}
$query .= "name";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Name</td>
  <td>Actual setting</td>";
if ($g_version > 82) {
  $buffer .= "
  <td>Unit</td>";
}
$buffer .= "
  <td>Category</td>
  <td>Short desc</td>
  <td>Extra desc</td>
  <td>Context</td>
  <td>Vartype</td>
  <td>Source</td>
  <td>Min value</td>
  <td>Max value</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['name']."</td>
  <td>".$row['setting']."</td>";
if ($g_version > 82) {
  $buffer .= "
  <td>".$row['unit']."</td>";
}
$buffer .= "
  <td>".$row['category']."</td>
  <td>".$row['short_desc']."</td>
  <td>".$row['extra_desc']."</td>
  <td>".$row['context']."</td>
  <td>".$row['vartype']."</td>
  <td>".$row['source']."</td>
  <td>".$row['min_val']."</td>
  <td>".$row['max_val']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/param.html';
include 'lib/fileoperations.php';

?>
