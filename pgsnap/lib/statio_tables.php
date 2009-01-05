<?php

/*
 * Copyright (c) 2008-2009 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Statistical IO Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  schemaname,
  relname,
  heap_blks_read,
  heap_blks_hit,
  idx_blks_read,
  idx_blks_hit,
  toast_blks_read,
  toast_blks_hit,
  tidx_blks_read,
  tidx_blks_hit
FROM pg_statio_all_tables";
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

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Schema</th>
  <th class="colMid">Table</th>
  <th class="colMid">Table Blocks Read</th>
  <th class="colMid">Table Blocks Hit</th>
  <th class="colMid">Toast Table Blocks Read</th>
  <th class="colMid">Toast Table Blocks Hit</th>
  <th class="colMid">Index Blocks Read</th>
  <th class="colMid">Index Blocks Hit</th>
  <th class="colMid">Toast Index Blocks Read</th>
  <th class="colMid">Toast Index Blocks Hit</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schemaname'])."
  <td>".$row['schemaname']."</td>
  <td>".$row['relname']."</td>
  <td>".$row['heap_blks_read']."</td>
  <td>".$row['heap_blks_hit']."</td>
  <td>".$row['idx_blks_read']."</td>
  <td>".$row['idx_blks_hit']."</td>
  <td>".$row['toast_blks_read']."</td>
  <td>".$row['toast_blks_hit']."</td>
  <td>".$row['tidx_blks_read']."</td>
  <td>".$row['tidx_blks_hit']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/statio_tables.html';
include 'lib/fileoperations.php';

?>
