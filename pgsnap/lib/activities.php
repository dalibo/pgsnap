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

$buffer = "<h1>Process list</h1>";


$query = "SELECT
  datname,
  procpid,
  client_addr,
  usename,
  current_query,";
if ($g_version >= 82) {
  $query .= "
  waiting,";
}
if ($g_version >= 83) {
  $query .= "
  xact_start,";
}
$query .="
  query_start,
  backend_start
FROM pg_stat_activity
ORDER BY datname, procpid";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Database name</td>
  <td>PID</td>
  <td>Client</td>
  <td>User</td>
  <td>Current query</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>Waiting</td>";
}
if ($g_version >= 83) {
  $buffer .= "
  <td>XACT start</td>";
}
$buffer .= "
  <td>Query start</td>
  <td>Backend start</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['datname']."</td>
  <td>".$row['procpid']."</td>
  <td>".$row['client_addr']."</td>
  <td>".$row['usename']."</td>
  <td>".$row['current_query']."</td>";
if ($g_version >= 82) {
  $buffer .= "
  <td>".$image[$row['waiting']]."</td>";
}
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['xact_start']."</td>";
}
$buffer .= "
  <td>".$row['query_start']."</td>
  <td>".$row['backend_start']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/activities.html';
include 'lib/fileoperations.php';

?>
