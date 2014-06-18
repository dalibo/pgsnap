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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Users</h1>
';

$query = "SELECT usename,
  usesysid,
  usecreatedb,
  usesuper,
  usecatupd,
  passwd,
  valuntil
FROM pg_user
ORDER BY usename";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">User name</th>
  <th class="colMid">User ID</th>
  <th class="colMid">Create DB?</th>
  <th class="colMid">Superuser?</th>
  <th class="colMid">Catalog update?</th>
  <th class="colMid">Valid until</th>
  <th class="colLast">Configuration</th>
</tr>
</thead>
<tbody>';

$query = "SELECT usename,
  usesysid,
  usecreatedb,
  usesuper,
  usecatupd,
  passwd,
  valuntil
FROM pg_user
ORDER BY usename";


while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['usename'].'</td>
  <td>'.$row['usesysid'].'</td>
  <td>'.$image[$row['usecreatedb']].'</td>
  <td>'.$image[$row['usesuper']].'</td>
  <td>'.$image[$row['usecatupd']].'</td>
  <td>'.$row['passwd'].'</td>
  <td>'.$row['valuntil'].'</td>
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

$filename = $outputdir.'/users.html';
include 'lib/fileoperations.php';

?>
