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

$buffer = "<h1>Statistical indexes list</h1>";

$buffer .= '<label><input id ="showusrobjects" type="checkbox" onclick="usrobjects();" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" onclick="sysobjects();" checked>Show System Objects</label>';

$query = "SELECT
  schemaname,
  relname,
  indexrelname,
  idx_scan,
  idx_tup_read,
  idx_tup_fetch
FROM pg_stat_all_indexes
ORDER BY schemaname, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Schema name</td>
  <td>Table name</td>
  <td>Index name</td>
  <td>idx_scan</td>
  <td>idx_tup_read</td>
  <td>idx_tup_fetch</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schemaname'])."
  <td>".$row['schemaname']."</td>
  <td>".$row['relname']."</td>
  <td>".$row['indexrelname']."</td>
  <td>".$row['idx_scan']."</td>
  <td>".$row['idx_tup_read']."</td>
  <td>".$row['idx_tup_fetch']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/stat_indexes.html';
include 'lib/fileoperations.php';

?>
