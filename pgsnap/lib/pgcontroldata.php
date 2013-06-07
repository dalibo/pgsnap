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

$buffer = $navigate_general.'
<div id="pgContentWrap">

<h1>pg_controldata</h1>
';

if (!strcmp($PGHOST, '127.0.0.1') || !strcmp($PGHOST, 'localhost')
  || strlen($PGHOST) == 0 || preg_match('/^\//', $PGHOST) == 1) {

  exec("LANG=C pg_controldata $g_datadirectory", $lines, $errorcode);

  if ($errorcode == 0) {
    $buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="30%">Variable</th>
  <th class="colLast" width="70%">Value</th>
</tr>
</thead>
<tbody>
';

    for ($index = 0; $index < count($lines); $index++) {
      $line = split(':', $lines[$index], 2);
      $buffer .= tr().'
  <td>'.trim($line[0]).'</td>
  <td>'.trim($line[1]).'</td>
</tr>';
    }
    $buffer .= '</tbody>
</table>
</div>
';
  } else {
    $buffer .= '<div class="warning">pg_controldata returns error code '.$errorcode.'!</div>';
  }
} else {
  $buffer .= '<div class="warning">Remote execution, so pg_controldata results unavailable!</div>';
}

$filename = $outputdir.'/pgcontroldata.html';
include 'lib/fileoperations.php';

?>
