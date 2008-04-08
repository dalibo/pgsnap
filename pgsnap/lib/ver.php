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

$buffer = "<h1>Installed products</h1>";

$buffer .= "<table>
<thead>
<tr>
  <td>Product</td>
  <td>Release</td>
</tr>
</thead>
<tbody>\n";

$query = "SELECT 'PostgreSQL' AS product, version() AS version;";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
$buffer .= "<tr>
  <td>".$row['product']."</td>
  <td>".$row['version']."</td>
</tr>";
}

if ($g_pgpool) {
  $buffer .= "<tr>
  <td>pgPool</td>
  <td><i>Tool</i></td>
</tr>";
}
if ($g_pgbuffercache) {
  $buffer .= "<tr>
  <td>pg_buffercache</td>
  <td><i>Contrib module</i></td>
</tr>";
}
if ($g_pgstattuple) {
  $buffer .= "<tr>
  <td>pgstattuple</td>
  <td><i>Contrib module</i></td>
</tr>";
}

$buffer .= "</tbody>
</table>";

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/ver.html';
include 'lib/fileoperations.php';

?>
