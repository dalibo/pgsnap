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

$buffer = "<h2>Locks List</h2>";


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

$buffer .= "<table>
<thead>
<tr>
  <td>Lock Type</td>
  <td>Database</td>
  <td>Relation</td>
  <td>Page</td>
  <td>Tuple</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>Virtual XID</td>";
}
$buffer .= "
  <td>Transaction ID</td>
  <td>Class ID</td>
  <td>Obj ID</td>
  <td>Obj Sub ID</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>Virtual Transaction</td>";
}
$buffer .= "
  <td>PID</td>
  <td>Mode</td>
  <td>Granted?</td>
</tr>
</thead>
<tbody>\n";

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
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/locks.html';
include 'lib/fileoperations.php';

?>
