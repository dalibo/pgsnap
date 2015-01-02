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

<h1>Archiver statistics</h1>
';

$query = "SELECT
  archived_count,
  last_archived_wal,
  date_trunc('second', last_archived_time) as last_archived_time,
  failed_count,
  last_failed_wal,
  date_trunc('second', last_failed_time) as last_failed_time,
  date_trunc('second', stats_reset) as stats_reset
FROM pg_stat_archiver";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst"># archived</th>
  <th class="colMid">Last archived WAL</th>
  <th class="colMid">Last archived time</th>
  <th class="colMid"># failed</th>
  <th class="colMid">Last failed WAL</th>
  <th class="colMid">Last failed time</th>
  <th class="colMid">Stats reset</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['archived_count']."</td>
  <td>".$row['last_archived_wal']."</td>
  <td>".$row['last_archived_time']."</td>
  <td>".$row['failed_count']."</td>
  <td>".$row['last_failed_wal']."</td>
  <td>".$row['last_failed_time']."</td>
  <td>".$row['stats_reset']."</td>
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

$filename = $outputdir.'/stat_archiver.html';
include 'lib/fileoperations.php';

?>
