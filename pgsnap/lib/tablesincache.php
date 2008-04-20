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

$buffer = "<h1>Tables in cache</h1>";

$buffer .= '<label><input id ="showusrobjects" type="checkbox" onclick="usrobjects();" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" onclick="sysobjects();" checked>Show System Objects</label>';

$query = "SELECT
  n.nspname,
  c.relname,
  pg_relation_size(c.oid) AS size,
  count(*) AS buffers
FROM pg_buffercache b, pg_class c, pg_namespace n
WHERE b.relfilenode = c.relfilenode
  AND c.relnamespace = n.oid
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

$buffer .= "<table>
<thead>
<tr>
  <td>Table Name</td>
  <td>Table size</td>
  <td>Total buffers</td>
  <td>Total buffers size</td>
  <td>% of the table in cache</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td>".$row['relname']."</td>
  <td>".$row['size']."</td>
  <td>".$row['buffers']."</td>
  <td>".($row['buffers']*8192)."</td>
  <td>".round(($row['buffers']*8192*100/$row['size']), 2)."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tablesincache.html';
include 'lib/fileoperations.php';

?>
