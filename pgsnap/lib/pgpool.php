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

$buffer = "<h1>pgPool Configuration</h1>";


$query = "show pool_status";

$rows = @pg_query($connection, $query);
if (!$rows) {
  echo "  pgPool not installed.\n";
  exit;
}

$buffer .= "<table>
<thead>
<tr>
  <td>Item</td>
  <td>Value</td>
  <td>Description</td>
</tr>
</thead>
<tbody>\n";

while ($row = pg_fetch_array($rows)) {
$buffer .= "<tr>
  <td>".$row['item']."</td>
  <td>".$row['value']."</td>
  <td>".$row['description']."</td>
</tr>";
}
$buffer .= "</tbody>
</table>";

$filename = $outputdir.'/pgpool.html';
include 'lib/fileoperations.php';

?>
