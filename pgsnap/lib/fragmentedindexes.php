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

<h1>Fragmented Indexes</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

// relam 403 is btree index
$query = "SELECT nspname, relname
FROM pg_class, pg_namespace
WHERE relkind = 'i' and  relnamespace=pg_namespace.oid and relam=403";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY relname";
$queries = $query;

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Index Name</th>
  <th class="colMid">Version</th>
  <th class="colMid">Tree Level</th>
  <th class="colMid">Index Size</th>
  <th class="colMid">Root Block No</th>
  <th class="colMid">Internal Pages</th>
  <th class="colMid">Leaf Pages</th>
  <th class="colMid">Empty Pages</th>
  <th class="colMid">Deleted Pages</th>
  <th class="colMid">Average Leaf Density</th>
  <th class="colLast">Leaf Fragmentation</th>
</tr>
</thead>
<tbody>
';

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

    $buffer .= tr($row['nspname'])."
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

$buffer .= '</tbody>
</table>
</div>
';

$queries .= "<br/>".$query;

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$queries.'</p>
</div>';

$filename = $outputdir.'/fragmentedindexes.html';
include 'lib/fileoperations.php';

?>
