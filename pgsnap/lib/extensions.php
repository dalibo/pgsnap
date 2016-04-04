<?php

/*
 * Copyright (c) 2008-2016 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Extensions</h1>
';

$query = "SELECT e.name,
  e.default_version,
  x.extversion AS installed_version,
  r.rolname as owner,
  n.nspname as namespace,
  x.extrelocatable,
  x.extconfig,
  x.extcondition,
  e.comment
FROM pg_available_extensions() e(name, default_version, comment)
LEFT JOIN pg_extension x ON e.name = x.extname
LEFT JOIN pg_roles r ON r.oid=x.extowner
LEFT JOIN pg_namespace n ON n.oid=x.extnamespace
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
  <th class="colFirst">Extension Name</th>
  <th class="colMid">Default version</th>
  <th class="colMid">Installed version</th>
  <th class="colMid">Owner</th>
  <th class="colMid">Namespace</th>
  <th class="colMid">Is Relocatable?</th>
  <th class="colMid">Config</th>
  <th class="colMid">Condition</th>
  <th class="colLast">Comments</th>
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['name']."</td>
  <td>".$row['default_version']."</td>
  <td>".$row['installed_version']."</td>
  <td>".$row['owner']."</td>
  <td>".$row['namespace']."</td>";
if ($row['extrelocatable'] != '') {
$buffer .= "
  <td>".$image[$row['extrelocatable']]."</td>";
} else {
$buffer .= "
  <td></td>";
}
$buffer .= "
  <td>".$row['extconfig']."</td>
  <td>".$row['extcondition']."</td>
  <td>".$row['comment']."</td>
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

$filename = $outputdir.'/extensions.html';
include 'lib/fileoperations.php';

?>
