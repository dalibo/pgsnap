<?php

/*
 * Copyright (c) 2008 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Total objects per tablespace</h1>
';


$query = "SELECT rolname,
  CASE WHEN relkind='r' THEN 'table'
       WHEN relkind='i' THEN 'index'
       WHEN relkind='S' THEN 'sequence'
       WHEN relkind='t' THEN 'TOAST table'
       ELSE '<unkown>' END AS kind,
  pg_size_pretty(SUM(pg_relation_size(pg_class.oid))::int8) AS total
FROM pg_class, pg_roles
WHERE pg_roles.oid=relowner
  AND relkind IN ('r', 't', 'i', 'S')
GROUP BY 1, 2
ORDER BY 1, 2";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst" width="40%">Owner</th>
  <th class="colMid" width="40%">Object\'s type</th>
  <th class="colLast" width="20%">Size</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['rolname'].'</td>
  <td>'.ucfirst($row['kind']).'</td>
  <td>'.$row['total'].'</td>
</tr>';
}
$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/user2.html';
include 'lib/fileoperations.php';

?>
