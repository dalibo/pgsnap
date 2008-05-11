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

<h1>Locks List</h1>
';


$query = "SELECT
 locktype,
 database::regclass as database,
 relation::regclass as relation,
 page,
 tuple,";
if ($g_version >= 83) {
$query .= "
 virtualxid,";
}
$query .= "
 transactionid,
 classid,
 objid,
 objsubid,";
if ($g_version >= 83) {
$query .= "
 virtualtransaction,";
}
$query .= "
 pid,
 mode,
 granted
FROM pg_locks";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Lock Type</th>
  <th class="colMid">Database</th>
  <th class="colMid">Relation</th>
  <th class="colMid">Page</th>
  <th class="colMid">Tuple</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">Virtual XID</th>';
}
$buffer .= '
  <th class="colMid">Transaction ID</th>
  <th class="colMid">Class ID</th>
  <th class="colMid">Obj ID</th>
  <th class="colMid">Obj Sub ID</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">Virtual Transaction</th>';
}
$buffer .= '
  <th class="colMid">PID</th>
  <th class="colMid">Mode</th>
  <th class="colLast">Granted?</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['locktype']."</td>
  <td>".$row['database']."</td>
  <td>".$row['relation']."</td>
  <td>".$row['page']."</td>
  <td>".$row['tuple']."</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['virtualxid']."</td>";
}
$buffer .= "
  <td>".$row['transactionid']."</td>
  <td>".$row['classid']."</td>
  <td>".$row['objid']."</td>
  <td>".$row['objsubid']."</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['virtualtransaction']."</td>";
}
$buffer .= "
  <td>".$row['pid']."</td>
  <td>".$row['mode']."</td>
  <td>".$image[$row['granted']]."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/locks.html';
include 'lib/fileoperations.php';

?>
