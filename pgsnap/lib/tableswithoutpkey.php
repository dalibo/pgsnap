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

$buffer = "<h1>Tables without PKEY list</h1>";

$buffer .= '<label><input id ="showusrobjects" type="checkbox" onclick="usrobjects();" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" onclick="sysobjects();" checked>Show System Objects</label>';

$query = "SELECT
  rolname AS owner,
  nspname,
  relname,
  pg_relation_size(pg_class.oid) as size
FROM pg_class, pg_roles, pg_namespace
WHERE
  relkind='r'
  AND relhaspkey IS false
  AND relowner = pg_roles.oid
  AND relnamespace = pg_namespace.oid
ORDER BY relowner, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Table Owner</td>
  <td>Schema Name</td>
  <td>Table Name</td>
  <td>Size</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td>".$row['owner']."</td>
  <td>".$row['nspname']."</td>
  <td>".$row['relname']."</td>
  <td>".$row['size']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tableswithoutpkey.html';
include 'lib/fileoperations.php';

?>
