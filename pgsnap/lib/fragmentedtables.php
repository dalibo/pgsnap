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

<h1>Fragmented Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT pg_class.oid, nspname, relname
FROM pg_class, pg_namespace
WHERE relkind IN ('r', 't')
  AND relnamespace = pg_namespace.oid";
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
  <th class="colFirst">Table Name</th>
  <th class="colMid">Table Length</th>
  <th class="colMid">Tuple count</th>
  <th class="colMid">Tuple Length</th>
  <th class="colMid">Tuple percent</th>
  <th class="colMid">Dead Tuple Count</th>
  <th class="colMid">Dead Tuple Length</th>
  <th class="colMid">Dead Tuple Percent</th>
  <th class="colMid">Free Space</th>
  <th class="colLast">Free Percent</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
  $query_stattuple = "SELECT
  table_len,
  tuple_count,
  tuple_len,
  tuple_percent,
  dead_tuple_count,
  dead_tuple_len,
  dead_tuple_percent,
  free_space,
  free_percent
FROM pgstattuple(".$row['oid'].")
WHERE table_len>0";

  $rows_stattuple = pg_query($connection, $query_stattuple);
  if (!$rows_stattuple) {
    echo "An error occured.\n";
    exit;
  }

  if (pg_num_rows($rows_stattuple) > 0) {
    $row_stattuple = pg_fetch_array($rows_stattuple);

    $buffer .= tr($row['nspname'])."
  <td>".$row['relname']."</td>
  <td>".$row_stattuple['table_len']."</td>
  <td>".$row_stattuple['tuple_count']."</td>
  <td>".$row_stattuple['tuple_len']."</td>
  <td>".$row_stattuple['tuple_percent']."</td>
  <td>".$row_stattuple['dead_tuple_count']."</td>
  <td>".$row_stattuple['dead_tuple_len']."</td>
  <td>".$row_stattuple['dead_tuple_percent']."</td>
  <td>".$row_stattuple['free_space']."</td>
  <td>".$row_stattuple['free_percent']."</td>
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
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fragmentedtables.html';
include 'lib/fileoperations.php';

?>
