<?php

/*
 * Copyright (c) 2008-2009 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Databases</h1>';

$query = 'SELECT datname,
  pg_get_userbyid(datdba) AS dba,
  pg_catalog.pg_encoding_to_char(encoding) AS encoding,
  datistemplate,
  datallowconn,';
if ($g_version > 80) {
  $query .= '
  datconnlimit,';
}
$query .= '
  datlastsysoid,
  datfrozenxid,';
if ($g_version > 74) {
  $query .= '
  spcname as tablespace,';
}
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(pg_database_size(datname)) AS size,';
}
$query .= '
  datconfig,
  datacl';
if ($g_version > 81) {
  $query .= ',
  age(datfrozenxid) AS freezeage, ROUND(100*(age(datfrozenxid)/freez::float)) AS perc';
}
$query .= '
FROM pg_database';
if ($g_version > 74) {
  $query .= ', pg_tablespace';
}
if ($g_version > 81) {
  $query .= "
JOIN (SELECT setting AS freez FROM pg_settings
      WHERE name = 'autovacuum_freeze_max_age') AS param
      ON (true)";
}
if ($g_version > 74) {
  $query .= '
WHERE dattablespace = pg_tablespace.oid';
}
$query .= '
ORDER BY datname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst" width="200">DB Owner</th>
  <th class="colMid" width="200">DB Name</th>
  <th class="colMid" width="200">Encoding</th>
  <th class="colMid" width="100">Template?</th>
  <th class="colMid" width="100">Allow connections?</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid" width="100">Connection limits</th>';
}
$buffer .= '
  <th class="colMid" width="100">Last system OID</th>
  <th class="colMid" width="100">Frozen XID</th>';
if ($g_version > 74) {
  $buffer .= '
  <th class="colMid" width="200">Tablespace name</th>';
}
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid" width="200">Size</th>';
}
if ($g_version > 81) {
  $buffer .= '
  <th class="colMid" width="200">Auto Freeze</th>';
}
$buffer .= '
  <th class="colMid" width="200">Configuration</th>
  <th class="colLast" width="300"><acronym title="Access Control List">ACL</acronym></th>
</tr>
</thead>
<tbody>';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['dba'].'</td>
  <td>'.$row['datname'].'</td>
  <td>'.$row['encoding'].'</td>
  <td>'.$image[$row['datistemplate']].'</td>
  <td>'.$image[$row['datallowconn']].'</td>';
if ($g_version > 80) {
  $buffer .= '
  <td>'.$row['datconnlimit'].'</td>';
}
$buffer .= '
  <td>'.$row['datlastsysoid'].'</td>
  <td>'.$row['datfrozenxid'].'</td>';
if ($g_version > 74) {
  $buffer .= '
  <td>'.$row['tablespace'].'</td>';
}
if ($g_version > 80) {
  $buffer .= '
  <td>'.$row['size'].'</td>';
}
if ($g_version > 81) {
  $buffer .= '
  <td>'.$row['freezeage'].' ('.$row['perc'].' %)</td>';
}
$buffer .= '
  <td>'.$row['datconfig'].'</td>
  <td><acronym X=\"Access Control List\">'.$row['datacl'].'</acronym></td>
</tr>';
}
$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/bases.html';
include 'lib/fileoperations.php';

?>
