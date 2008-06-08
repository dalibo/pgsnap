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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Tablespaces</h1>
';

$query = "SELECT spcname,
  pg_get_userbyid(spcowner) AS owner,
  spclocation,
  spcacl";
if ($g_superuser) {
  if ($g_version > 80) {
    $query .= ',
  pg_size_pretty(pg_tablespace_size(spcname)) AS size';
  } else {
    $query .= ',
  (SELECT SUM(relpages)*8192 FROM pg_class WHERE reltablespace=pg_tablespace.oid ) AS size';
  }
}
$query .= '
FROM pg_tablespace
ORDER BY spcname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst" width="20%">Tablespace Owner</th>
  <th class="colMid" width="20%">Tablespace Name</th>
  <th class="colMid" width="20%">Location</th>';
if ($g_superuser) {
  if ($g_version > 80) {
    $buffer .= '
  <th class="colMid" width="200">Size</th>';
  } else {
    $buffer .= '
  <th class="colMid" width="200">Estimated Size</th>';
  }
}
$buffer .= '
  <th class="colLast" width="20%">ACL</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
  $buffer .= tr()."
  <td>".$row['owner']."</td>
  <td>".$row['spcname']."</td>
  <td>".$row['spclocation']."</td>";
  if ($g_superuser) {
    $buffer .= "
  <td>".$row['size']."</td>";
  }
  $buffer .= "
  <td>".$row['spcacl']."</td>
</tr>";
}
$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tablespaces.html';
include 'lib/fileoperations.php';

?>
