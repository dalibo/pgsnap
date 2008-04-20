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

$buffer = "<h1>Fragmented Indexes</h1>";


$query = "SELECT nspname, relname
FROM pg_class, pg_namespace
WHERE relkind = 'i' and  relnamespace=pg_namespace.oid
ORDER BY relname";
$queries = $query;

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Index Name</td>
  <td>Version</td>
  <td>Tree Level</td>
  <td>Index Size</td>
  <td>Root Block No</td>
  <td>Internal Pages</td>
  <td>Leaf Pages</td>
  <td>Empty Pages</td>
  <td>Deleted Pages</td>
  <td>Average Leaf Density</td>
  <td>Leaf Fragmentation</td>
</tr>
</thead>
<tbody>\n";
while ($row = pg_fetch_array($rows)) {
  $query_statindex = "SELECT
  version,
  tree_level,
  index_size,
  root_block_no,
  internal_pages,
  leaf_pages,
  empty_pages,
  deleted_pages,
  avg_leaf_density,
  leaf_fragmentation
FROM pgstatindex('".pg_escape_string($row['nspname'].'.'.$row['relname'])."')
WHERE index_size>0";

  $rows_statindex = pg_query($connection, $query_statindex);
  if (!$rows_statindex) {
    echo "An error occured.\n";
    //exit;
  }

  if (pg_num_rows($rows_statindex) > 0) {
    $row_statindex = pg_fetch_array($rows_statindex);

    $buffer .= tr()."
  <td>".$row['relname']."</td>
  <td>".$row_statindex['version']."</td>
  <td>".$row_statindex['tree_level']."</td>
  <td>".$row_statindex['index_size']."</td>
  <td>".$row_statindex['root_block_no']."</td>
  <td>".$row_statindex['internal_pages']."</td>
  <td>".$row_statindex['leaf_pages']."</td>
  <td>".$row_statindex['empty_pages']."</td>
  <td>".$row_statindex['deleted_pages']."</td>
  <td>".$row_statindex['avg_leaf_density']."</td>
  <td>".$row_statindex['leaf_fragmentation']."</td>
</tr>";
  }
}
$buffer .= "</tbody>
</table>";
$queries .= "<br/>".$query;

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$queries.'</p>
</div>';

$filename = $outputdir.'/fragmentedindexes.html';
include 'lib/fileoperations.php';

?>
