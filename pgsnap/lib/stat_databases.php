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

$buffer = $navigate_stats.'
<div id="pgContentWrap">

<h1>Statistical Databases</h1>
';

$query = "SELECT
  datname,
  numbackends,
  xact_commit,
  xact_rollback,
  blks_read,
  blks_hit";
if ($g_version >= 83) {
$query .= ",
  tup_returned,
  tup_fetched,
  tup_inserted,
  tup_updated,
  tup_deleted";
}
if ($g_version >= 91) {
$query .= ",
  conflicts,
  date_trunc('second', stats_reset) as stats_reset";
}
$query .= "
FROM pg_stat_database
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
  <th class="colMid">Number of backends</th>
  <th class="colMid">XACT commit</th>
  <th class="colMid">XACT rollback</th>
  <th class="colMid">Blocks read</th>
  <th class="colMid">Blocks hit</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">Tuples returned</th>
  <th class="colMid">Tuples fetched</th>
  <th class="colMid">Tuples inserted</th>
  <th class="colMid">Tuples updated</th>
  <th class="colMid">Tuples deleted</th>';
}
if ($g_version >= 91) {
  $buffer .= '
  <th class="colMid">Conflicts</th>
  <th class="colMid">Stats reset</th>';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td title=\"".$comments['databases'][$row['datname']]."\">".$row['datname']."</td>
  <td>".$row['numbackends']."</td>
  <td>".$row['xact_commit']."</td>
  <td>".$row['xact_rollback']."</td>
  <td>".$row['blks_read']."</td>
  <td>".$row['blks_hit']."</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['tup_returned']."</td>
  <td>".$row['tup_fetched']."</td>
  <td>".$row['tup_inserted']."</td>
  <td>".$row['tup_updated']."</td>
  <td>".$row['tup_deleted']."</td>";
}
if ($g_version >= 91) {
  $buffer .= "
  <td>".$row['conflicts']."</td>
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

$filename = $outputdir.'/stat_databases.html';
include 'lib/fileoperations.php';

?>
