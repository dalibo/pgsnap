<?php

/*
 * Copyright (c) 2008-2013 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Roles</h1>
';

$query = "SELECT rolname,
  rolsuper,
  rolinherit,
  rolcreaterole,
  rolcreatedb,
  rolcatupdate,
  rolcanlogin,
  rolconnlimit,";
if ($g_version > 90) {
  $query .= "
  rolreplication,";
}
$query .= "
  rolvaliduntil,
  rolconfig
FROM pg_roles
ORDER BY rolname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Role name</th>
  <th class="colMid">Super user?</th>
  <th class="colMid">Inherit?</th>
  <th class="colMid">Create role?</th>
  <th class="colMid">Create DB?</th>
  <th class="colMid">Catalog update?</th>
  <th class="colMid">Can login?</th>';
if ($g_version > 90) {
  $buffer .= '
  <th class="colMid">Replication?</th>';
}
$buffer .= '
  <th class="colMid">Connection limits</th>
  <th class="colMid">Valid until</th>
  <th class="colLast">Configuration</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td title="'.$comments['roles'][$row['rolname']].'">'.$row['rolname'].'</td>
  <td>'.$image[$row['rolsuper']].'</td>
  <td>'.$image[$row['rolinherit']].'</td>
  <td>'.$image[$row['rolcreaterole']].'</td>
  <td>'.$image[$row['rolcreatedb']].'</td>
  <td>'.$image[$row['rolcatupdate']].'</td>
  <td>'.$image[$row['rolcanlogin']].'</td>';
if ($g_version > 90) {
$buffer .= '
  <td>'.$image[$row['rolreplication']].'</td>';
}
$buffer .= '
  <td>'.$row['rolconnlimit'].'</td>
  <td>'.$row['rolvaliduntil'].'</td>
  <td>'.$row['rolconfig'].'</td>
</tr>';
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/roles.html';
include 'lib/fileoperations.php';

?>
