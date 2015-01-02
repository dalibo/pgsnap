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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Tables With 5+ Indexes</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  schemaname,
  tablename,
  count(*) as total
FROM pg_indexes";
if ($g_withoutsysobjects) {
  $query .= "
WHERE schemaname <> 'pg_catalog'
  AND schemaname <> 'information_schema'
  AND schemaname !~ '^pg_toast'";
}
$query .= "
GROUP BY 1, 2
HAVING count(*)>=5
ORDER BY 1, 2";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Table Name</th>
  <th class="colMid">Schema Name</th>
  <th class="colLast">Total</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schemaname'])."
  <td title=\"".$comments['relations'][$row['schemaname']][$row['tablename']]."\">".$row['tablename']."</td>
  <td title=\"".$comments['schemas'][$row['schemaname']]."\">".$row['schemaname']."</td>
  <td>".$row['total']."</td>
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

$filename = $outputdir.'/tableswith5+indexes.html';
include 'lib/fileoperations.php';

?>
