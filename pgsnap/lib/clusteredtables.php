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

<h1>Clustered Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT s.relname,
  s.indexrelname,
  a.attname,
  p.correlation
FROM pg_stats p,
  pg_stat_user_indexes s,
  pg_index i,
  pg_attribute a
WHERE
  i.indisclustered
  AND s.indexrelid = i.indexrelid
  AND p.tablename = s.relname
  AND a.attnum = ANY (indkey)
  AND a.attrelid = i.indrelid
  AND p.attname = a.attname
ORDER BY 1, 2, 3";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Table</th>
  <th class="colMid">Index</th>
  <th class="colMid">Column</th>
  <th class="colLast">Correlation</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['relname'])."
  <td>".$row['relname']."</td>
  <td>".$row['indexrelname']."</td>
  <td>".$row['attname']."</td>
  <td>".$row['correlation']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/clusteredtables.html';
include 'lib/fileoperations.php';

?>
