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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Tables In Cache</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  n.nspname,
  c.relname,";
if ($g_version > 80) {
  $query .= '
  pg_relation_size(c.oid) AS size,';
} else {
  $query .= '
  sum(c.relpages)*8192 AS size,';
}
$query .= "
  count(*) AS buffers
FROM pg_buffercache b, pg_class c, pg_namespace n
WHERE b.relfilenode = c.relfilenode
  AND c.relnamespace = n.oid";
if ($g_withoutsysobjects) {
  $query .= "
  AND n.nspname <> 'pg_catalog'
  AND n.nspname <> 'information_schema'
  AND n.nspname !~ '^pg_toast'";
}
$query .= "
  AND c.relkind = 'r'
  AND b.reldatabase IN (0, (SELECT oid FROM pg_database
                            WHERE datname = current_database()))
GROUP BY n.nspname, c.relname, size
ORDER BY 3 DESC";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Table Name</th>
';
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid">Table Size</th>';
} else {
  $buffer .= '
  <th class="colMid">Estimated TableSize</th>';
}
$buffer .= '
  <th class="colMid">Total buffers</th>
  <th class="colMid">Total buffers size</th>
  <th class="colLast">% of the table in cache</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td>".$row['relname']."</td>
  <td>".$row['size']."</td>
  <td>".$row['buffers']."</td>
  <td>".($row['buffers']*8192)."</td>
  <td>".round(($row['buffers']*8192*100/$row['size']), 2)."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tablesincache.html';
include 'lib/fileoperations.php';

?>
