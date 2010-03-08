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

$buffer = $navigate_activities.'
<div id="pgContentWrap">

<h1>Non Idle Processes</h1>
';


$query = "SELECT
  extract(epoch FROM (now() - query_start))::numeric(10,2) AS age,
  procpid,
  usename,";
if ($g_version > 80) {
  $query .= "
  client_addr,";
}
$query .= "
  current_query
FROM pg_stat_activity
WHERE current_query <> '<IDLE>'
ORDER BY 1";

echo $query;
$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Age</th>
  <th class="colMid">PID</th>
  <th class="colMid">User</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid">Client</th>';
}
$buffer .= '
  <th class="colLast">Query</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['age']."</td>
  <td>".$row['procpid']."</td>
  <td>".$row['usename']."</td>";
if ($g_version > 80) {
  $buffer .= "
  <td>".$row['client_addr']."</td>";
}
$buffer .= "
  <td>".$row['current_query']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/nonidleprocesses.html';
include 'lib/fileoperations.php';

?>
