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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Databases In Cache</h1>
';

$query = "SELECT
  pg_get_userbyid(datdba) AS dba,
  datname,
  pg_database_size(reldatabase) AS size,
  count(*) AS buffers
FROM pg_buffercache, pg_database
WHERE reldatabase=pg_database.oid
GROUP BY 1, 2, 3
ORDER BY 1, 2, 3";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="20%">Database Owner</th>
  <th class="colMid" width="20%">Database Name</th>
  <th class="colMid" width="20%">Database Size</th>
  <th class="colMid" width="15%">Total Buffers</th>
  <th class="colMid" width="15%">Total Buffers Size</th>
  <th class="colLast" width="10%">% of Database In Cache</th>
</tr>
</thead>
<tbody>';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['dba'].'</td>
  <td title="'.$comments['databases'][$row['datname']].'">'.$row['datname'].'</td>
  <td>'.$row['size'].'</td>
  <td>'.$row['buffers'].'</td>
  <td>'.($row['buffers']*8192).'</td>
  <td>'.round(($row['buffers']*8192*100/$row['size']), 2).'</td>
</tr>';
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/databasesincache.html';
include 'lib/fileoperations.php';

?>
