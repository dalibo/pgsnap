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

<h1>Indexes Bigger Than Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT n.nspname AS schemaname,
  c.relname AS tablerelname,
  i.relname AS indexrelname,
  pg_size_pretty(pg_relation_size(c.oid)) AS tablesize,
  pg_size_pretty(pg_relation_size(i.oid)) AS indexsize
FROM pg_class c
  JOIN pg_index x ON c.oid = x.indrelid
  JOIN pg_class i ON i.oid = x.indexrelid
  LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE c.relkind IN ('r', 't')
  AND pg_relation_size(c.oid) < pg_relation_size(i.oid)
  AND pg_relation_size(c.oid) > 1048576";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY 1, 2, 3";


$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  $buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Schema</th>
  <th class="colMid">Table</th>
  <th class="colMid">Index</th>
  <th class="colMid">Table Size</th>
  <th class="colLast">Index Size</th>
</tr>
</thead>
<tbody>
';

  while ($row = pg_fetch_array($rows)) {
    $buffer .= tr($row['schemaname'])."
  <td title=\"".$comments['schemas'][$row['schemaname']]."\">".$row['schemaname']."</td>
  <td title=\"".$comments['relations'][$row['schemaname']][$row['tablerelname']]."\">".$row['tablerelname']."</td>
  <td title=\"".$comments['relations'][$row['schemaname']][$row['indexrelname']]."\">".$row['indexrelname']."</td>
  <td>".$row['tablesize']."</td>
  <td>".$row['indexsize']."</td>
</tr>";
  }
  $buffer .= '</tbody>
  </table>
</div>
';
} else {
  $buffer .= '<div class="warning">No table of more than 1 MB!</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/indexesbiggerthantables.html';
include 'lib/fileoperations.php';

?>
