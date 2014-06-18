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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>FK Without indexes</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  pg_get_userbyid(relowner) AS tableowner,
  nspname,
  conrelid AS tableoid,
  relname AS tablename,
  regexp_replace(
    pg_get_constraintdef(pg_constraint.oid, true),
    'FOREIGN KEY \(([^\)]*)\)(.*)',
    E'\\\\1')
    AS columnnames,
  conname
FROM
  pg_constraint
JOIN pg_class ON conrelid=pg_class.oid
JOIN pg_namespace ON relnamespace=pg_namespace.oid
WHERE
      contype = 'f'";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY 1, 2, 4";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Table Owner</th>
  <th class="colMid">Schema name</th>
  <th class="colMid">Table name</th>
  <th class="colMid">Constraint Name</th>
  <th class="colLast">Columns Names</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
  $query_indexes = "SELECT
  attrelid,
  string_agg(attname, ', ' ORDER BY attnum)
FROM pg_attribute
WHERE
  attrelid IN (SELECT indexrelid
               FROM pg_index
               WHERE indrelid=".$row['tableoid'].")
GROUP BY attrelid
HAVING string_agg(attname, ', ' ORDER BY attnum) = '".$row['columnnames']."'";

$rows_indexes = pg_query($connection, $query_indexes);
  if ($rows_indexes && pg_num_rows($rows_indexes) == 0) {
  $buffer .= tr($row['nspname'])."
  <td title=\"".$comments['roles'][$row['tableowner']]."\">".$row['tableowner']."</td>
  <td title=\"".$comments['schemas'][$row['nspname']]."\">".$row['nspname']."</td>
  <td title=\"".$comments['relations'][$row['nspname']][$row['tablename']]."\">".$row['tablename']."</td>
  <td>".$row['conname']."</td>
  <td>".$row['columnnames']."</td>
</tr>";
  }
}

$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fkwithoutindexes.html';
include 'lib/fileoperations.php';

?>
