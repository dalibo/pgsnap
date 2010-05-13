<?php

/*
 * Copyright (c) 2008-2010 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>FSM Relations List</h1>
';

$query = "SELECT
  sum(interestingpages) as ip,
  sum(storedpages) as sp
FROM pg_freespacemap_relations";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
  if ($row['ip'] > $row['sp']) {
    $buffer .= '<div class="warning">You should increase the max_fsm_pages parameter (max_fsm_pages is set to '.$g_settings['max_fsm_pages'].' and there\'s already '.$row['ip'].' pages interesting to track in the FSM cache!).</div>';
  }
}

pg_free_result($rows);

$query = "SELECT
  coalesce(spcname, fsm.reltablespace::text) as spcname,
  coalesce(datname, fsm.reldatabase::text) as datname,
  coalesce(relname, fsm.relfilenode::text) as relname,
  avgrequest,
  interestingpages,
  storedpages,
  nextpage
FROM pg_freespacemap_relations AS fsm
  LEFT JOIN pg_tablespace ON fsm.reltablespace = pg_tablespace.oid
  LEFT JOIN pg_database ON fsm.reldatabase = pg_database.oid
  LEFT JOIN pg_class ON fsm.relfilenode = pg_class.relfilenode
ORDER BY 2, 3";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > $g_settings['max_fsm_relations']*0.9
  and pg_num_rows($rows) < $g_settings['max_fsm_relations']*1.1) {
  $buffer .= '<div class="warning">You should increase the max_fsm_relations parameter (max_fsm_relations is set to '.$g_settings['max_fsm_relations'].' and there\'s already '.pg_num_rows($rows).' relations in the FSM cache!).</div>';
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Tablespace</th>
  <th class="colMid">Database</th>
  <th class="colMid">Relation</th>
  <th class="colMid">Average Request</th>
  <th class="colMid">Interesting Pages</th>
  <th class="colMid">Stored Pages</th>
  <th class="colLast">Next Page</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['spcname'].'</td>
  <td title="'.$comments['databases'][$row['datname']].'">'.$row['datname'].'</td>
  <td>'.$row['relname'].'</td>
  <td>'.$row['avgrequest'].'</td>
  <td>'.$row['interestingpages'].'</td>
  <td>'.$row['storedpages'].'</td>
  <td>'.$row['nextpage'].'</td>
</tr>';
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/fsmrelations.html';
include 'lib/fileoperations.php';

?>
