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

<h1>Statistical Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  schemaname,
  relname,
  seq_scan,
  seq_tup_read,
  idx_scan,
  idx_tup_fetch,
  n_tup_ins,
  n_tup_upd,
  n_tup_del";
if ($g_version >= 83) {
  $query .= ",
  n_tup_hot_upd,
  n_live_tup,
  n_dead_tup";
}
if ($g_version >= 94) {
  $query .= ",
  n_mod_since_analyze";
}
if ($g_version >= 82) {
  $query .= ",
  last_vacuum,
  last_autovacuum,
  last_analyze,
  last_autoanalyze";
}
if ($g_version >= 91) {
  $query .= ",
  vacuum_count,
  autovacuum_count,
  analyze_count,
  autoanalyze_count";
}
$query .= "
FROM pg_stat_all_tables";
if ($g_withoutsysobjects) {
  $query .= "
WHERE schemaname <> 'pg_catalog'
  AND schemaname <> 'information_schema'
  AND schemaname !~ '^pg_toast'";
}
$query .= "
ORDER BY schemaname, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Schema name</th>
  <th class="colMid">Table name</th>
  <th class="colMid">seq_scan</th>
  <th class="colMid">seq_tup_read</th>
  <th class="colMid">idx_scan</th>
  <th class="colMid">idx_tup_fetch</th>
  <th class="colMid">n_tup_ins</th>
  <th class="colMid">n_tup_upd</th>
  <th class="colMid">n_tup_del</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colMid">n_tup_hot_upd</th>
  <th class="colMid">n_live_tup</th>
  <th class="colMid">n_dead_tup</th>';
}
if ($g_version >= 94) {
  $buffer .= '
  <th class="colMid">n_mod_since_analyze</th>';
}
if ($g_version >= 82) {
$buffer .= '
  <th class="colMid">last_vacuum</th>
  <th class="colMid">last_autovacuum</th>
  <th class="colMid">last_analyze</th>
  <th class="colLast">last_autoanalyze</th>';
}
if ($g_version >= 91) {
$buffer .= '
  <th class="colMid">vacuum_count</th>
  <th class="colMid">autovacuum_count</th>
  <th class="colMid">analyze_count</th>
  <th class="colLast">autoanalyze_count</th>';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schemaname'])."
  <td title=\"".$comments['schemas'][$row['schemaname']]."\">".$row['schemaname']."</td>
  <td title=\"".$comments['relations'][$row['schemaname']][$row['relname']]."\">".$row['relname']."</td>
  <td>".$row['seq_scan']."</td>
  <td>".$row['seq_tup_read']."</td>
  <td>".$row['idx_scan']."</td>
  <td>".$row['idx_tup_fetch']."</td>
  <td>".$row['n_tup_ins']."</td>
  <td>".$row['n_tup_upd']."</td>
  <td>".$row['n_tup_del']."</td>";
if ($g_version >= 83) {
  $buffer .= "
  <td>".$row['n_tup_hot_upd']."</td>
  <td>".$row['n_live_tup']."</td>
  <td>".$row['n_dead_tup']."</td>";
}
if ($g_version >= 94) {
  $buffer .= "
  <td>".$row['n_mod_since_analyze']."</td>";
}
if ($g_version >= 82) {
$buffer .= "
  <td>".$row['last_vacuum']."</td>
  <td>".$row['last_autovacuum']."</td>
  <td>".$row['last_analyze']."</td>
  <td>".$row['last_autoanalyze']."</td>";
}
if ($g_version >= 91) {
$buffer .= "
  <td>".$row['vacuum_count']."</td>
  <td>".$row['autovacuum_count']."</td>
  <td>".$row['analyze_count']."</td>
  <td>".$row['autoanalyze_count']."</td>";
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

$filename = $outputdir.'/stat_tables.html';
include 'lib/fileoperations.php';

?>
