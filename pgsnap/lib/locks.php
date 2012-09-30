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

$buffer = $navigate_activities.'
<div id="pgContentWrap">

<h1>Locks</h1>
';


$query = "SELECT";
if ($g_version > 80) {
  $query .= "
 locktype,";
}
$query .= "
 CASE WHEN datname IS NOT NULL THEN datname
      ELSE database::text
 END AS database,
 nspname,
 relname,";
if ($g_version > 80) {
  $query .= "
 page,
 tuple,";
  if ($g_version >= 83) {
  $query .= "
 virtualxid,";
  }
  $query .= "
 transactionid,
 classid,
 objid,
 objsubid,";
  if ($g_version >= 83) {
  $query .= "
 virtualtransaction,";
  }
} else {
  $query .= "
 transaction,";
}
$query .= "
 pid,
 mode,";
if ($g_version > 91) {
  $query .= "
  fastpath,";
}
$query .= "
 granted
FROM pg_locks
 LEFT JOIN pg_database ON pg_database.oid = database
 LEFT JOIN pg_class ON pg_class.oid = relation
 LEFT JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colFirst">Lock Type</th>';
}
$buffer .= '
  <th class="colMid">Database</th>
  <th class="colMid">Relation</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colMid">Page</th>
  <th class="colMid">Tuple</th>';
  if ($g_version >= 83) {
    $buffer .= '
  <th class="colMid">Virtual XID</th>';
  }
  $buffer .= '
  <th class="colMid">Transaction ID</th>
  <th class="colMid">Class ID</th>
  <th class="colMid">Obj ID</th>
  <th class="colMid">Obj Sub ID</th>';
  if ($g_version >= 83) {
    $buffer .= '
  <th class="colMid">Virtual Transaction</th>';
  }
} else {
  $buffer .= '
  <th class="colMid">Transaction</th>';
}
$buffer .= '
  <th class="colMid">PID</th>
  <th class="colMid">Mode</th>';
if ($g_version > 91) {
  $buffer .= '
  <th class="colMid">Fastpath</th>';
}
$buffer .= '
  <th class="colLast">Granted?</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr();
if ($g_version > 80) {
  $buffer .= "
  <td>".$row['locktype']."</td>";
}
$buffer .= "
  <td";
if (array_key_exists($row['database'], $comments['databases'])) {
  $buffer .= " title=\"".$comments['databases'][$row['database']]."\"";
}
$buffer .= ">".$row['database']."</td>
  <td";
if (array_key_exists($row['nspname'], $comments['relations'])) {
  if (array_key_exists($row['relname'], $comments['relations'][$row['nspname']])) {
    $buffer .= " title=\"".$comments['relations'][$row['nspname']][$row['relname']]."\"";
  }
}
$buffer .= ">".$row['nspname'].".".$row['relname']."</td>";
if ($g_version > 80) {
  $buffer .= "
  <td>".$row['page']."</td>
  <td>".$row['tuple']."</td>";
  if ($g_version >= 83) {
    $buffer .= "
    <td>".$row['virtualxid']."</td>";
  }
  $buffer .= "
  <td>".$row['transactionid']."</td>
  <td>".$row['classid']."</td>
  <td>".$row['objid']."</td>
  <td>".$row['objsubid']."</td>";
  if ($g_version >= 83) {
    $buffer .= "
  <td>".$row['virtualtransaction']."</td>";
  }
} else {
  $buffer .= "
  <td>".$row['transaction']."</td>";
}
$buffer .= "
  <td>".$row['pid']."</td>
  <td>".$row['mode']."</td>";
if ($g_version > 91) {
  $buffer .= "
  <td>".$image[$row['fastpath']]."</td>";
}
$buffer .= "
  <td>".$image[$row['granted']]."</td>
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

$filename = $outputdir.'/locks.html';
include 'lib/fileoperations.php';

?>
