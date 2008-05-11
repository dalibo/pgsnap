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

$buffer = $navigate_activities.'
<div id="pgContentWrap">

<h1>Process List</h1>
';


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
  date_trunc('second', xact_start) as xact_start,";
}
$query .="
  date_trunc('second', query_start) as query_start,
  date_trunc('second', backend_start) as backend_start
FROM pg_stat_activity
ORDER BY datname, procpid";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">DB name</th>
  <th class="colMid">PID</th>
  <th class="colMid">Client</th>
  <th class="colMid">User</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">Waiting</th>';
}
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">XACT start</th>';
}
$buffer .= '
  <th class="colMid">Query start</th>
  <th class="colLast">Backend start</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['datname']."</td>
  <td>".$row['procpid']."</td>
  <td>".$row['client_addr']."</td>
  <td>".$row['usename']."</td>";
if ($g_version >= 82) {
  $buffer .= "
  <td>".$image[$row['waiting']]."</td>";
}
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['xact_start']."</td>";
}
$buffer .= '
  <td title="'.$row['current_query'].'">'.$row['query_start'].'</td>
  <td>'.$row['backend_start'].'</td>
</tr>';
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/activities.html';
include 'lib/fileoperations.php';

?>
