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

$buffer = $navigate_stats.'
<div id="pgContentWrap">

<h1>Statistical Indexes</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  schemaname,
  relname,
  indexrelname,
  idx_scan,
  idx_tup_read,
  idx_tup_fetch
FROM pg_stat_all_indexes";
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
  <th class="colMid">Index name</th>
  <th class="colMid">idx_scan</th>
  <th class="colMid">idx_tup_read</th>
  <th class="colLast">idx_tup_fetch</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schemaname'])."
  <td title=\"".$comments['schemas'][$row['schemaname']]."\">".$row['schemaname']."</td>
  <td title=\"".$comments['relations'][$row['schemaname']][$row['relname']]."\">".$row['relname']."</td>
  <td>".$row['indexrelname']."</td>
  <td>".$row['idx_scan']."</td>
  <td>".$row['idx_tup_read']."</td>
  <td>".$row['idx_tup_fetch']."</td>
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

$filename = $outputdir.'/stat_indexes.html';
include 'lib/fileoperations.php';

?>
