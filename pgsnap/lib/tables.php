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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Tables</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  relname,
  nspname AS schema,
  pg_get_userbyid(relowner) AS owner,";
if ($g_version >= 90) {
  $query .= "
  reloftype,";
}
$query .= "
  relam,
  relfilenode,";
if ($g_version > 74) {
  $query .= "
  (select spcname from pg_tablespace where oid=reltablespace) as tablespace,";
}
$query .= "
  relpages,
  reltuples,";
if ($g_version > 91) {
  $query .= "
  relallvisible,";
}
$query .= "
  reltoastrelid,
  reltoastidxid,
  relhasindex,
  relisshared,
  relkind,
  relnatts,
  relchecks,";
if ($g_version < 84) {
  $query .= "
  reltriggers,
  relukeys,
  relfkeys,
  relrefs,";
}
elseif ($g_version >= 91) {
  $query .= "
  CASE WHEN relpersistence='t' THEN 'Temporary' ELSE 'Unknown' END AS relpersistence,";
}
else {
  $query .= "
  relistemp,";
}
$query .= "
  relhasoids,
  relhaspkey,";
if ($g_version == 90) {
  $query .= "
  relhasexclusion,";
}
$query .= "
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
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(pg_relation_size(pg_class.oid)) AS size';
} else {
  $query .= '
  relpages*8192 AS size';
}
$query .= "
FROM pg_class, pg_namespace
WHERE relkind = 'r'
  AND relnamespace = pg_namespace.oid";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Table name</th>
  <th class="colMid">Schema name</th>
  <th class="colMid">Table Owner</th>';
if ($g_version >= 90) {
  $buffer .= '
  <th class="colMid">Of Type</th></th>';
}
$buffer .= '
  <th class="colMid">relam</th>
  <th class="colMid">relfilenode</th>';
if ($g_version > 74) {
  $buffer .= '
  <th class="colMid">Tablespace name</th>';
}
$buffer .= '
  <th class="colMid">Pages #</th>';
if ($g_version > 91) {
  $buffer .= '
  <th class="colMid">All-visible Pages #</th>';
}
$buffer .= '
  <th class="colMid">Tuples #</th>
  <th class="colMid">OID Toast Table</th>
  <th class="colMid">OID Toast Index</th>
  <th class="colMid">Has index?</th>
  <th class="colMid">Is shared?</th>
  <th class="colMid">Kind</th>
  <th class="colMid">Number of Attributes</th>
  <th class="colMid">Checks</th>';
if ($g_version < 84) {
$buffer .= '
  <th class="colMid">Triggers</th>
  <th class="colMid">Unique Keys</th>
  <th class="colMid">Foreign Keys</th>
  <th class="colMid">Refs</th>';
} elseif ($g_version >= 91) {
$buffer .= '
  <th class="colMid">Persistence</th>';
} else {
$buffer .= '
  <th class="colMid">Is Temp</th>';
}
$buffer .= '
  <th class="colMid">Has OID?</th>
  <th class="colMid">Has Primary Key?</th>';
if ($g_version == 90) {
  $buffer .= '
  <th class="colMid">Has Exclusion Constraint</th>';
}
$buffer .= '
  <th class="colMid">Has Rules?</th>
  <th class="colMid">Has subclass?</th>';
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
if ($g_version > 80) {
  $buffer .= '
  <th class="colLast" width="200">Size</th>';
} else {
  $buffer .= '
  <th class="colLast" width="200">Estimated Size</th>';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['schema'])."
  <td title=\"".$comments['relations'][$row['schema']][$row['relname']]."\">".$row['relname']."</td>
  <td title=\"".$comments['schemas'][$row['schema']]."\">".$row['schema']."</td>
  <td title=\"".$comments['roles'][$row['owner']]."\">".$row['owner']."</td>";
if ($g_version >= 90) {
  $buffer .= "
  <td>".$row['reloftype']."</td>";
}
$buffer .= "
  <td>".$row['relam']."</td>
  <td>".$row['relfilenode']."</td>";
if ($g_version > 74) {
  $buffer .= "
  <td>".$row['tablespace']."</td>";
}
$buffer .= "
  <td>".$row['relpages']."</td>";
if ($g_version > 91) {
  $buffer .= "
  <td>".$row['relallvisible']."</td>";
}
$buffer .= "
  <td>".$row['reltuples']."</td>
  <td>".$row['reltoastrelid']."</td>
  <td>".$row['reltoastidxid']."</td>
  <td>".$image[$row['relhasindex']]."</td>
  <td>".$image[$row['relisshared']]."</td>
  <td>".$kind[$row['relkind']]."</td>
  <td>".$row['relnatts']."</td>
  <td>".$row['relchecks']."</td>";
if ($g_version < 84) {
  $buffer .= "
  <td>".$row['reltriggers']."</td>
  <td>".$row['relukeys']."</td>
  <td>".$row['relfkeys']."</td>
  <td>".$row['relrefs']."</td>";
}
else if ($g_version >= 91) {
  $buffer .= "
  <td>".$row['relpersistence']."</td>";
}
else {
  $buffer .= "
  <td>".$image[$row['relistemp']]."</td>";
}
$buffer .= "
  <td>".$image[$row['relhasoids']]."</td>
  <td>".$image[$row['relhaspkey']]."</td>";
if ($g_version == 90) {
  $buffer .= "
  <td>".$image[$row['relhasexclusion']]."</td>";
}
$buffer .= "
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
if ($g_version > 80) {
  $buffer .= "
  <td>".$row['size']."</td>
</tr>";
} else {
  $buffer .= "
  <td>".pretty_size($row['size'])."</td>
</tr>";
}
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tables.html';
include 'lib/fileoperations.php';

?>
