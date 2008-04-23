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

$buffer = "<h2>Roles list</h2>";


$query = "SELECT rolname,
  rolsuper,
  rolinherit,
  rolcreaterole,
  rolcreatedb,
  rolcatupdate,
  rolcanlogin,
  rolconnlimit,
  rolvaliduntil,
  rolconfig
FROM pg_roles
ORDER BY rolname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Role name</td>
  <td>Super user?</td>
  <td>Inherit?</td>
  <td>Create role?</td>
  <td>Create DB?</td>
  <td>Catalog update?</td>
  <td>Can login?</td>
  <td>Connection limits</td>
  <td>Valid until</td>
  <td>Configuration</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['rolname']."</td>
  <td>".$image[$row['rolsuper']]."</td>
  <td>".$image[$row['rolinherit']]."</td>
  <td>".$image[$row['rolcreaterole']]."</td>
  <td>".$image[$row['rolcreatedb']]."</td>
  <td>".$image[$row['rolcatupdate']]."</td>
  <td>".$image[$row['rolcanlogin']]."</td>
  <td>".$row['rolconnlimit']."</td>
  <td>".$row['rolvaliduntil']."</td>
  <td>".$row['rolconfig']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/roles.html';
include 'lib/fileoperations.php';

?>
