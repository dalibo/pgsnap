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

$buffer = "<h2>FSM Pages List</h2>";


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

$buffer .= "<table>
<thead>
<tr>
  <td>Tablespace</td>
  <td>Database</td>
  <td>Relation</td>
  <td>Block Number</td>
  <td>Bytes</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['spcname'].'</td>
  <td>'.$row['datname'].'</td>
  <td>'.$row['relname'].'</td>
  <td>'.$row['relblocknumber'].'</td>
  <td>'.$row['bytes'].'</td>
</tr>';
}
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fsmpages.html';
include 'lib/fileoperations.php';

?>
