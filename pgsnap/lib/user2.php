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

$buffer = "<h1>Total objects per tablespace</h1>";


$query = "SELECT rolname,
  CASE WHEN relkind='r' THEN 'table'
       WHEN relkind='i' THEN 'index'
       WHEN relkind='S' THEN 'sequence'
       WHEN relkind='t' THEN 'table TOAST'
       ELSE '<unkown>' END AS kind,
  SUM(pg_relation_size(pg_class.oid)) AS total
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

$buffer .= "<table>
<thead>
<tr>
  <td>Owner</td>
  <td>Object's type</td>
  <td>Size</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= "<tr>
  <td>".$row['rolname']."</td>
  <td>".$row['kind']."</td>
  <td>".$row['total']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$filename = $outputdir.'/user2.html';
include 'lib/fileoperations.php';

?>
