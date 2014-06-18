<?php

/*
 * Copyright (c) 2008-2014 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Materialized views</h1>
';

$query = "SELECT
  relname,
  nspname AS schema,
  pg_get_userbyid(relowner) AS owner,
  relam,
  relfilenode,
  (select spcname from pg_tablespace where oid=reltablespace) as tablespace,
  relpages,
  reltuples,
  reltoastrelid,
  reltoastidxid,
  relhasindex,
  relisshared,
  relispopulated,
  relkind,
  relnatts,
  relchecks,
  relhasoids,
  relhaspkey,
  relhasrules,
  relhassubclass,
  relfrozenxid,
  relacl,
  reloptions,
  pg_size_pretty(pg_relation_size(pg_class.oid)) AS size
FROM pg_class, pg_namespace
WHERE relkind = 'm'
  AND relnamespace = pg_namespace.oid
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'
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
  <th class="colMid">Is populated?</th>
  <th class="colMid">Kind</th>
  <th class="colMid">natts</th>
  <th class="colMid">Checks</th>
  <th class="colMid">Has OID?</th>
  <th class="colMid">Has Primary Key?</th>
  <th class="colMid">Has Rules?</th>
  <th class="colMid">Has subclass?</th>
  <th class="colMid">Frozen XID</th>
  <th class="colMid"><acronym X=\"Access Control List\">ACL</acronym></th>
  <th class="colMid">Options</th>
  <th class="colLast" width="200">Size</th>
</tr>
</thead>
<tbody>
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
  <td>".$image[$row['relispopulated']]."</td>
  <td>".$row['relkind']."</td>
  <td>".$row['relnatts']."</td>
  <td>".$row['relchecks']."</td>
  <td>".$image[$row['relhasoids']]."</td>
  <td>".$image[$row['relhaspkey']]."</td>
  <td>".$image[$row['relhasrules']]."</td>
  <td>".$image[$row['relhassubclass']]."</td>
  <td>".$row['relfrozenxid']."</td>
  <td><acronym X=\"Access Control List\">".$row['relacl']."</acronym></td>
  <td>".$row['reloptions']."</td>
  <td>".$row['size']."</td>
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

$filename = $outputdir.'/materializedviews.html';
include 'lib/fileoperations.php';

?>
