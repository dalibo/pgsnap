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

$buffer = "<h1>pg_config Results</h1>";

if (!strcmp($PGHOST, '127.0.0.1') or !strcmp($PGHOST, 'localhost')) {
  exec('pg_config', $lignes);

  $buffer .= "<table>
<thead>
<tr>
  <td>Variable</td>
  <td>Value</td>
</tr>
</thead>
<tbody>\n";

  for ($index = 0; $index < count($lignes); $index++) {
    $ligne = split('=', $lignes[$index], 2);
    $buffer .= "<tr>
  <td>".$ligne[0]."</td>
  <td>".$ligne[1]."</td>
</tr>";
  }
  $buffer .= "</tbody>
</table>";
} else {
  $buffer .= '<div id="warning">Remote execution, so pg_config results unavailable!</div>';
}

$filename = $outputdir.'/pgconfig.html';
include 'lib/fileoperations.php';

?>
