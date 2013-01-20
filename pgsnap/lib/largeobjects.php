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

<h1>Large Objects</h1>
';


$query = "SELECT
  loid,
  count(*) AS totalblocks
FROM pg_largeobject
GROUP BY 1
ORDER BY 1";

if ($g_version >= 90) {
$query = "SELECT
  loid,
  pg_get_userbyid(lomowner) AS owner,
  lomacl,
  totalblocks
FROM pg_largeobject_metadata md,
(".$query.") pg_largeobject
WHERE md.oid=loid
ORDER BY 1";
}


$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="20%">LO Oid</th>';
if ($g_version >= 90) {
$buffer .= '
  <th class="colMid">Owner</th>
  <th class="colMid">ACL</th>';
}
$buffer .= '
  <th class="colLast" width="20%">Total Blocks</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['loid']."</td>";
if ($g_version >= 90) {
$buffer .= "
  <td>".$row['owner']."</td>
  <td>".$row['lamacl']."</td>";
}
$buffer .= "
  <td>".$row['totalblocks']."</td>
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

$filename = $outputdir.'/largeobjects.html';
include 'lib/fileoperations.php';

?>
