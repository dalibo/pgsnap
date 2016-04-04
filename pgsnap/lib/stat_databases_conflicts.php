<?php

/*
 * Copyright (c) 2008-2016 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Replication Conflicts on Databases</h1>
';

$query = "SELECT
  datname,
  confl_tablespace,
  confl_lock,
  confl_snapshot,
  confl_bufferpin,
  confl_deadlock
FROM pg_stat_database_conflicts
ORDER BY datname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Database name</th>
  <th class="colMid">Tablespace conflicts</th>
  <th class="colMid">Lock conflicts</th>
  <th class="colMid">Snapshot conflicts</th>
  <th class="colMid">Bufferpin conflicts</th>
  <th class="colMid">Deadlock conflicts</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td title=\"".$comments['databases'][$row['datname']]."\">".$row['datname']."</td>
  <td>".$row['confl_tablespace']."</td>
  <td>".$row['confl_lock']."</td>
  <td>".$row['confl_snapshot']."</td>
  <td>".$row['confl_bufferpin']."</td>
  <td>".$row['confl_deadlock']."</td>
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

$filename = $outputdir.'/stat_databases_conflicts.html';
include 'lib/fileoperations.php';

?>
