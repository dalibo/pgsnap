<?php

/*
 * Copyright (c) 2008-2015 Guillaume Lelarge <guillaume@lelarge.info>
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

$buffer = $navigate_stats.'
<div id="pgContentWrap">

<h1>bgwriter</h1>
';


$query = "SELECT
  checkpoints_timed,
  checkpoints_req,
  buffers_checkpoint,
  buffers_clean,
  maxwritten_clean,
  buffers_backend,";
if ($g_version >= 91) {
  $query .= "
  buffers_backend_fsync,";
}
$query .= "
  buffers_alloc";
if ($g_version >= 91) {
  $query .= ",
  date_trunc('second', stats_reset) as stats_reset";
}
$query .= "
FROM pg_stat_bgwriter";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Checkpoints Timed Out</th>
  <th class="colMid">Checkpoints Requested</th>
  <th class="colMid">Buffers Freed by Checkpoint</th>
  <th class="colMid">Buffers Cleaned by bgwriter</th>
  <th class="colMid">Maxwritten Before complete clean</th>
  <th class="colMid">Buffers freed by backends</th>';
if ($g_version >= 91) {
  $buffer .= '
  <th class="colMid">Fsync done by backends</th>
';
}
if ($g_version >= 91) {
  $buffer .= '
  <th class="colMid">Buffers allocated</th>
  <th class="colLast">Stats Reset</th>
';
} else {
$buffer .= '
  <th class="colLast">Buffers allocated</th>';
}
$buffer .= '

</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['checkpoints_timed']."</td>
  <td>".$row['checkpoints_req']."</td>
  <td>".$row['buffers_checkpoint']."</td>
  <td>".$row['buffers_clean']."</td>
  <td>".$row['maxwritten_clean']."</td>
  <td>".$row['buffers_backend']."</td>";
if ($g_version >= 91) {
  $buffer .= "
  <td>".$row['buffers_backend_fsync']."</td>";
}
$buffer .= "
  <td>".$row['buffers_alloc']."</td>";
if ($g_version >= 91) {
  $buffer .= "
  <td>".$row['stats_reset']."</td>";
}
$buffer .= "
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

$filename = $outputdir.'/bgwriter.html';
include 'lib/fileoperations.php';

?>
