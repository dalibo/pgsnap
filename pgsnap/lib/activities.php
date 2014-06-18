<?php

/*
 * Copyright (c) 2008-2014 Guillaume Lelarge <guillaume@lelarge.info>
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


if ($g_version > 91) {
    $pid = 'pid';
    $current_query = 'query';
} else {
    $pid = 'procpid';
    $current_query = 'current_query';
}
$query = "SELECT
  datname,
  $pid,
  usename,
  $current_query,
  date_trunc('second', query_start) as query_start";
if ($g_version > 90) {
  $query .= ",
  client_hostname";
}
if ($g_version > 80) {
  $query .= ",
  client_addr";
}
if ($g_version >= 82) {
  $query .= ",
  waiting";
}
if ($g_version >= 83) {
  $query .= ",
  date_trunc('second', xact_start) as xact_start";
}
if ($g_version >= 82) {
$query .= ",
  date_trunc('second', backend_start) as backend_start";
}
if ($g_version >= 90) {
  $query .= ",
  application_name";
}
$query .= "
FROM pg_stat_activity
ORDER BY datname, $pid";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">DB name</th>
  <th class="colMid">PID</th>';
if ($g_version >= 90) {
  $buffer .= '
  <th class="colMid">Application name</th>';
}
if ($g_version > 90) {
  $buffer .= '
  <th class="colMid">Client hostname</th>';
}
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid">Client</th>';
}
$buffer .= '
  <th class="colMid">User</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">Waiting</th>';
}
if ($g_version >= 82) {
  $buffer .= '
  <th class="colMid">Backend start</th>';
}
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">XACT start</th>';
}
$buffer .= '
  <th class="colMid">Query start</th>
  <th class="colLast">Query</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td title=\"".$comments['databases'][$row['datname']]."\">".$row['datname']."</td>
  <td>".$row[$pid]."</td>";
if ($g_version >= 90) {
  $buffer .= "
  <td>".$row['application_name']."</td>";
}
if ($g_version > 90) {
  $buffer .= "
  <td>".$row['client_hostname']."</td>";
}
if ($g_version > 80) {
  $buffer .= "
  <td>".$row['client_addr']."</td>";
}
$buffer .= "
  <td title=\"".$comments['roles'][$row['usename']]."\">".$row['usename']."</td>";
if ($g_version >= 82) {
  $buffer .= "
  <td>".$image[$row['waiting']]."</td>";
}
if ($g_version >= 82) {
  $buffer .= '
  <td>'.$row['backend_start'].'</td>';
}
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['xact_start']."</td>";
}
$buffer .= '
  <td>'.$row['query_start'].'</td>
  <td>'.$row[$current_query].'</td>
</tr>';
}

$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/activities.html';
include 'lib/fileoperations.php';

?>
