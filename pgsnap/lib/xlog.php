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

<h1>WAL files</h1>
';

$query = "SELECT
 *
FROM pg_ls_dir('pg_xlog')
WHERE pg_ls_dir ~ E'^[0-9A-F]{24}$'
ORDER BY 1";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Filename</th>
  <th class="colMid">Size</th>
  <th class="colMid">Last access time</th>
  <th class="colMid">Last modification time</th>
  <th class="colMid">Last status change time</th>
  <th class="colMid">Creation time</th>
  <th class="colLast">Directory?</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
  $query2 = "SELECT
  size,
  access,
  modification,
  change,
  creation,
  isdir
FROM pg_stat_file('pg_xlog/".$row[0]."')";
  $query .= "<br/>$query2";

  $rows2 = pg_query($connection, $query2);
  if (!$rows2) {
    echo "An error occured.\n";
    exit;
  }

  $row2 = pg_fetch_array($rows2);
  $buffer .= tr()."
  <td>".$row[0]."</td>
  <td>".$row2['size']."</td>
  <td>".$row2['access']."</td>
  <td>".$row2['modification']."</td>
  <td>".$row2['change']."</td>
  <td>".$row2['creation']."</td>
  <td>".$image[$row2['isdir']]."</td>
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

$filename = $outputdir.'/xlog.html';
include 'lib/fileoperations.php';

?>
