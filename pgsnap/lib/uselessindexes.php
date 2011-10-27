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

<h1>Useless Indexes</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT idstat.schemaname AS schema_name,
idstat.relname AS table_name,
indexrelname AS index_name,
idstat.idx_scan AS times_used,
pg_size_pretty(pg_relation_size(idstat.relid)) AS table_size,
pg_size_pretty(pg_relation_size(indexrelid)) AS index_size,
n_tup_upd + n_tup_ins + n_tup_del as num_writes,
indexdef AS definition
FROM pg_stat_user_indexes AS idstat JOIN pg_indexes ON indexrelname =
indexname
JOIN pg_stat_user_tables AS tabstat ON idstat.relname = tabstat.relname
WHERE idstat.idx_scan < 200
AND indexdef !~* 'unique'
AND pg_relation_size(idstat.indexrelid) > 1048576
ORDER BY idstat.relname, indexrelname;";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  $buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Schema name</th>
  <th class="colMid">Table name</th>
  <th class="colMid">Index name</th>
  <th class="colMid">Times Used</th>
  <th class="colMid">Table Size</th>
  <th class="colMid">Index Size</th>
  <th class="colMid">Times Written</th>
  <th class="colLast">Definition</th>
</tr>
</thead>
<tbody>
';

  while ($row = pg_fetch_array($rows)) {
    $buffer .= tr($row['table_name'])."
  <td title=\"".$comments['schemas'][$row['schema_name']]."\">".$row['schema_name']."</td>
  <td title=\"".$comments['relations'][$row['schema_name']][$row['table_name']]."\">".$row['table_name']."</td>
  <td title=\"".$comments['relations'][$row['schema_name']][$row['index_name']]."\">".$row['index_name']."</td>
  <td>".$row['times_used']."</td>
  <td>".$row['table_size']."</td>
  <td>".$row['index_size']."</td>
  <td>".$row['num_writes']."</td>
  <td>".$row['definition']."</td>
</tr>";
  }
  $buffer .= '</tbody>
  </table>
</div>
';
} else {
  $buffer .= '<div class="warning">No index of more than 1 MB!</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/uselessindexes.html';
include 'lib/fileoperations.php';

?>
