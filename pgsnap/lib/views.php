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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Views</h1>
';

$buffer .= '<label><input id ="showusrobjects" type="checkbox" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" checked>Show System Objects</label>';

$query = "SELECT
  relname,
  nspname AS schema,
  rolname AS owner,
  relam,
  relfilenode,
  (select spcname from pg_tablespace where oid=reltablespace) as tablespace,
  relpages,
  reltuples,
  reltoastrelid,
  reltoastidxid,
  relhasindex,
  relisshared,
  reltype,
  relnatts,
  relchecks,
  reltriggers,
  relukeys,
  relfkeys,
  relrefs,
  relhasoids,
  relhaspkey,
  relhasrules,
  relhassubclass,";
if ($g_version >= 82) {
$query .= "
  relfrozenxid,";
}
$query .= "
  relacl,";
if ($g_version >= 82) {
$query .= "
  reloptions,";
}
$query .= "
  pg_size_pretty(pg_relation_size(pg_class.oid)) AS size
FROM pg_class, pg_roles, pg_namespace
WHERE relkind = 'v'
  AND relowner = pg_roles.oid
  AND relnamespace = pg_namespace.oid
  AND pg_table_is_visible(pg_class.oid)
ORDER BY relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Table name</th>
  <th class="colMid">Schema name</th>
  <th class="colMid">Table Owner</th>
  <th class="colMid">relam</th>
  <th class="colMid">relfilenode</th>
  <th class="colMid">Tablespace name</th>
  <th class="colMid">Pages #</th>
  <th class="colMid">Tuples #</th>
  <th class="colMid">OID Toast Table</th>
  <th class="colMid">OID Toast Index</th>
  <th class="colMid">Has index?</th>
  <th class="colMid">Is shared?</th>
  <th class="colMid">Kind</th>
  <th class="colMid">natts</th>
  <th class="colMid">Checks</th>
  <th class="colMid">Triggers</th>
  <th class="colMid">Unique Keys</th>
  <th class="colMid">Foreign Keys</th>
  <th class="colMid">Refs</th>
  <th class="colMid">Has OID?</th>
  <th class="colMid">Has Primary Key?</th>
  <th class="colMid">Has Rules?</th>
  <th class="colMid">Has subclass?</th>
';
if ($g_version >= 82) {
$buffer .= '
  <th class="colMid">Frozen XID</th>';
}
$buffer .= '
  <th class="colMid"><acronym X=\"Access Control List\">ACL</acronym></th>';
if ($g_version >= 82) {
$buffer .= '
  <th class="colMid">Options</th>';
}
$buffer .= '
  <th class="colLast">Size</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schema'])."
  <td>".$row['relname']."</td>
  <td>".$row['schema']."</td>
  <td>".$row['owner']."</td>
  <td>".$row['relam']."</td>
  <td>".$row['relfilenode']."</td>
  <td>".$row['tablespace']."</td>
  <td>".$row['relpages']."</td>
  <td>".$row['reltuples']."</td>
  <td>".$row['reltoastrelid']."</td>
  <td>".$row['reltoastidxid']."</td>
  <td>".$image[$row['relhasindex']]."</td>
  <td>".$image[$row['relisshared']]."</td>
  <td>".$row['relkind']."</td>
  <td>".$row['relnatts']."</td>
  <td>".$row['relchecks']."</td>
  <td>".$row['reltriggers']."</td>
  <td>".$row['relukeys']."</td>
  <td>".$row['relfkeys']."</td>
  <td>".$row['relrefs']."</td>
  <td>".$image[$row['relhasoids']]."</td>
  <td>".$image[$row['relhaspkey']]."</td>
  <td>".$image[$row['relhasrules']]."</td>
  <td>".$image[$row['relhassubclass']]."</td>";
if ($g_version >= 82) {
$buffer .= "
 <td>".$row['relfrozenxid']."</td>";
}
$buffer .= "
  <td><acronym X=\"Access Control List\">".$row['relacl']."</acronym></td>";
if ($g_version >= 82) {
$buffer .= "
 <td>".$row['reloptions']."</td>";
}
$buffer .= "
  <td>".$row['size']."</td>
</tr>";
}
$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/views.html';
include 'lib/fileoperations.php';

?>
