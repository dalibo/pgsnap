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

$buffer = "<h1>Indexes list</h1>";

$buffer .= '<label><input id ="showusrobjects" type="checkbox" onclick="usrobjects();" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" onclick="sysobjects();" checked>Show System Objects</label>';

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
WHERE relkind = 'i'
  AND relowner = pg_roles.oid
  AND relnamespace = pg_namespace.oid
  AND pg_table_is_visible(pg_class.oid)
ORDER BY relname";


$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Index name</td>
  <td>Schema name</td>
  <td>Table Owner</td>
  <td>relam</td>
  <td>relfilenode</td>
  <td>Tablespace name</td>
  <td>Pages #</td>
  <td>Tuples #</td>
  <td>OID Toast Table</td>
  <td>OID Toast Index</td>
  <td>Has index?</td>
  <td>Is shared?</td>
  <td>Kind</td>
  <td>natts</td>
  <td>Checks</td>
  <td>Triggers</td>
  <td>Unique Keys</td>
  <td>Foreign Keys</td>
  <td>Refs</td>
  <td>Has OID?</td>
  <td>Has Primary Key?</td>
  <td>Has Rules?</td>
  <td>Has subclass?</td>";
if ($g_version >= 82) {
$buffer .= "
  <td>Frozen XID</td>";
}
$buffer .= "
  <td><acronym X=\"Access Control List\">ACL</acronym></td>";
if ($g_version >= 82) {
$buffer .= "
  <td>Options</td>";
}
$buffer .= "
  <td>Size</td>
</tr>
</thead>
<tbody>\n";

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
$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/indexes.html';
include 'lib/fileoperations.php';

?>
