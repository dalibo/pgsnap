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

$buffer = "<h1>Languages list</h1>";

switch ($g_version) {
  case '83':
    $query = "SELECT lanname,
  rolname AS owner,
  lanispl,
  lanpltrusted,
  lanacl
FROM pg_language, pg_roles
WHERE lanowner = pg_roles.oid
ORDER BY lanname";
    break;
  default:
$query = "SELECT lanname,
  lanispl,
  lanpltrusted,
  lanacl
FROM pg_language
ORDER BY lanname";
}

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Language name</td>";
if ($g_version == '83') {
  $buffer .= "
  <td>Language owner</td>";
}
$buffer .= "
  <td>Is PL?</td>
  <td>Trusted PL?</td>
  <td><acronym X=\"Access Control List\">ACL</acronym></td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= "<tr>
  <td>".$row['lanname']."</td>";
if ($g_version == '83') {
  $buffer .= "
  <td>".$row['owner']."</td>";
}
$buffer .= "
  <td>".$image[$row['lanispl']]."</td>
  <td>".$image[$row['lanpltrusted']]."</td>
  <td><acronym X=\"Access Control List\">".$row['lanacl']."</acronym></td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$filename = $outputdir.'/languages.html';
include 'lib/fileoperations.php';

?>
