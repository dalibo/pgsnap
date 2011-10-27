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

<h1>Replication Process List</h1>
';


$query = "SELECT
  procpid,
  usename,
  application_name,
  client_addr,
  client_hostname,
  client_port,
  date_trunc('second', backend_start) as backend_start,
  state,
  sent_location,
  write_location,
  flush_location,
  replay_location,
  sync_priority,
  sync_state
FROM pg_stat_replication
ORDER BY application_name, procpid";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">PID</th>
  <th class="colMid">User name</th>
  <th class="colMid">Application name</th>
  <th class="colMid">Client address</th>
  <th class="colMid">Client hostname</th>
  <th class="colMid">Client port</th>
  <th class="colMid">Backend start</th>
  <th class="colMid">State</th>
  <th class="colMid">Sent location</th>
  <th class="colMid">Write location</th>
  <th class="colMid">Flush location</th>
  <th class="colMid">Replay location</th>
  <th class="colMid">Sync priority</th>
  <th class="colLast">Sync state</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td title=\"".$comments['databases'][$row['datname']]."\">".$row['datname']."</td>
  <td>".$row['procpid']."</td>
  <td title=\"".$comments['roles'][$row['usename']]."\">".$row['usename']."</td>
  <td>".$row['application_name']."</td>
  <td>".$row['client_addr']."</td>
  <td>".$row['client_hostname']."</td>
  <td>".$row['client_port']."</td>
  <td>".$row['backend_start']."</td>
  <td>".$row['state']."</td>
  <td>".$row['sent_location']."</td>
  <td>".$row['write_location']."</td>
  <td>".$row['flush_location']."</td>
  <td>".$row['replay_location']."</td>
  <td>".$row['sync_priority']."</td>
  <td>".$row['sync_state']."</td>
</tr>";
}

$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/replication.html';
include 'lib/fileoperations.php';

?>
