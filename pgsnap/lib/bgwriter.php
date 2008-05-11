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
  buffers_backend,
  buffers_alloc
FROM pg_stat_bgwriter";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Checkpoints Timed Out</th>
  <th class="colMid">Checkpoints Requested</th>
  <th class="colMid">Buffers Freed by Checkpoint</th>
  <th class="colMid">Buffers Cleaned by Checkpoint</th>
  <th class="colMid">Maxwritten Before complete clean</th>
  <th class="colMid">Buffers freed by backends</th>
  <th class="colLast">Buffers allocated</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['checkpoints_timed']."</td>
  <td>".$row['checkpoints_req']."</td>
  <td>".$row['buffers_checkpoint']."</td>
  <td>".$row['buffers_clean']."</td>
  <td>".$row['maxwritten_clean']."</td>
  <td>".$row['buffers_backend']."</td>
  <td>".$row['buffers_alloc']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/bgwriter.html';
include 'lib/fileoperations.php';

?>
