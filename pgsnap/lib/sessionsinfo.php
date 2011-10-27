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

<h1>Sessions</h1>
';

$query = "SELECT name, setting FROM pg_settings
  WHERE name IN ('max_connections', 'autovacuum_max_workers');";
$queries = $query;

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $$row['name'] = $row['setting'];
}

$buffer .= "<tr>
  <td>".$row['product']."</td>
  <td>".$row['version']."</td>
</tr>";

$query = "SELECT COUNT(*) FROM pg_stat_activity";
$queries .= "<br/>".$query;

$rows = @pg_query($connection, $query);
if ($rows) {
  $row = pg_fetch_array($rows);
  $count = $row[0];
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Actual sessions</th>
  <th class="colMid">Max connections</th>';
if ($g_version >= 83) {
  $buffer .= '
  <th class="colLast">Autovac Workers</th>';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

$buffer .= tr().'<td>';

if ($count > $max_connections*90/100) {
  $buffer .= '<div  class="danger">'.$count.'</div>';
} else {
  $buffer .= $count;
}

$buffer .= '</td>
  <td>'.$max_connections.'</td>';
if ($g_version >= 83) {
  $buffer .= '
  <td>'.$autovacuum_max_workers.'</td>';
}
$buffer .= '
</tr>
</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$queries.'</p>
</div>';

$filename = $outputdir.'/sessionsinfo.html';
include 'lib/fileoperations.php';

?>
