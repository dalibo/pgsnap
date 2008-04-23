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

$buffer = "<h2>Total objects per tablespace</h2>";


$query = "SELECT
  rolname,
  spcname,
  CASE WHEN relkind='r' THEN 'Tables' ELSE 'Index' END AS kind,
  count(*) AS total
FROM pg_class, pg_tablespace, pg_roles
WHERE spcowner=pg_roles.oid
  AND pg_tablespace.oid=reltablespace
  AND relkind IN ('r', 'i')
GROUP BY 1, 2, 3
ORDER BY 1, 2, 3;";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Owner</td>
  <td>Tablespace</td>
  <td>Object's type</td>
  <td>Count</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['rolname']."</td>
  <td>".$row['spcname']."</td>
  <td>".$row['kind']."</td>
  <td>".$row['total']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tblspc1.html';
include 'lib/fileoperations.php';

?>
