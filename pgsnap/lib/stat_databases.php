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

$buffer = "<h2>Statistical databases list</h2>";

$query = "SELECT
  datname,
  numbackends,
  xact_commit,
  xact_rollback,
  blks_read,
  blks_hit";
if ($g_version >= 83) {
$query .= ",
  tup_returned,
  tup_fetched,
  tup_inserted,
  tup_updated,
  tup_deleted";
}
$query .= "
FROM pg_stat_database
ORDER BY datname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Database name</td>
  <td>Number of backends</td>
  <td>XACT commit</td>
  <td>XACT rollback</td>
  <td>Blocks read</td>
  <td>Blocks hit</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>Tuple returned</td>
  <td>Tuple fetched</td>
  <td>Tuple inserted</td>
  <td>Tuple updated</td>
  <td>Tuple deleted</td>";
}
$buffer .= "
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['datname']."</td>
  <td>".$row['numbackends']."</td>
  <td>".$row['xact_commit']."</td>
  <td>".$row['xact_rollback']."</td>
  <td>".$row['blks_read']."</td>
  <td>".$row['blks_hit']."</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['tup_returned']."</td>
  <td>".$row['tup_fetched']."</td>
  <td>".$row['tup_inserted']."</td>
  <td>".$row['tup_updated']."</td>
  <td>".$row['tup_deleted']."</td>";
}
$buffer .= "
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/stat_databases.html';
include 'lib/fileoperations.php';

?>
