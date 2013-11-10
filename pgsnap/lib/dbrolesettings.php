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

<h1>Database/Role Settings</h1>
';


$query = "SELECT
  db.datname,
  r.rolname,
  setconfig 
FROM pg_db_role_setting
LEFT JOIN pg_database db ON db.oid=setdatabase
LEFT JOIN pg_roles r ON r.oid=setrole";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="30%">Database</th>
  <th class="colMid" width="30%">User</th>
  <th class="colLast" width="40%">Configuration</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
  $buffer .= tr();
  if (strlen($row['datname']) == 0) {
    $buffer .= "
  <td>".$row['datname']."</td>";
  } else {
    $buffer .= "
  <td title=\"".$comments['databases'][$row['datname']]."\">".$row['datname']."</td>";
  }
$buffer .= "
  <td>".$row['rolname']."</td>
  <td>".$row['setconfig']."</td>
</tr>";
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/dbrolesettings.html';
include 'lib/fileoperations.php';

?>
