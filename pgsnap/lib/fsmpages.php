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

$buffer = $navigate_stats.'
<div id="pgContentWrap">

<h1>FSM Pages List</h1>
';


$query = "SELECT
  coalesce(spcname, fsm.reltablespace::text) as spcname,
  coalesce(datname, fsm.reldatabase::text) as datname,
  coalesce(relname, fsm.relfilenode::text) as relname,
  relblocknumber,
  bytes
FROM pg_freespacemap_pages AS fsm
  LEFT JOIN pg_tablespace ON fsm.reltablespace = pg_tablespace.oid
  LEFT JOIN pg_database ON fsm.reldatabase = pg_database.oid
  LEFT JOIN pg_class ON fsm.relfilenode = pg_class.relfilenode
ORDER BY 2, 3, 4";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Tablespace</th>
  <th class="colMid">Database</th>
  <th class="colMid">Relation</th>
  <th class="colMid">Block Number</th>
  <th class="colLast">Bytes</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['spcname'].'</td>
  <td title="'.$comments['databases'][$row['datname']].'">'.$row['datname'].'</td>
  <td>'.$row['relname'].'</td>
  <td>'.$row['relblocknumber'].'</td>
  <td>'.$row['bytes'].'</td>
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

$filename = $outputdir.'/fsmpages.html';
include 'lib/fileoperations.php';

?>
