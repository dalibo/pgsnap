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

$buffer = "<h1>Fragmented Tables</h1>";


$query = "SELECT oid, relname
FROM pg_class
WHERE relkind IN ('r', 't')
ORDER BY relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Table Name</td>
  <td>Table Length</td>
  <td>Tuple count</td>
  <td>Tuple Length</td>
  <td>Tuple percent</td>
  <td>Dead Tuple count</td>
  <td>Dead Tuple Length</td>
  <td>Dead Tuple percent</td>
  <td>Free Space</td>
  <td>Free percent</td>
</tr>
</thead>
<tbody>\n";

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

    $buffer .= "<tr>
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
$buffer .= "</tbody>
</table>";

$filename = $outputdir.'/fragmentedtables.html';
include 'lib/fileoperations.php';

?>
