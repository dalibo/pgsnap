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

$buffer = $navigate_activities.'
<div id="pgContentWrap">

<h1>Prepared Transactions</h1>';


$query = "SELECT
 transaction,
 gid,
 prepared,
 owner,
 database
FROM pg_prepared_xacts
ORDER BY owner, database";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Owner</th>
  <th class="colMid">Database</th>
  <th class="colMid">Transaction ID</th>
  <th class="colMid">GID</th>
  <th class="colLast">Prepared Time</th>
</tr>
</thead>
<tbody>
';
 
while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td title=\"".$comments['roles'][$row['rolname']]."\">".$row['rolname']."</td>
  <td title=\"".$comments['databases'][$row['database']]."\">".$row['database']."</td>
  <td>".$row['transaction']."</td>
  <td>".$row['gid']."</td>
  <td>".$row['prepared']."</td>
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

$filename = $outputdir.'/preparedxacts.html';
include 'lib/fileoperations.php';

?>
