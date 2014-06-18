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

$buffer = $navigate_general.'
<div id="pgContentWrap">

<h1>pg_config</h1>

';

if (!strcmp($PGHOST, '127.0.0.1') || !strcmp($PGHOST, 'localhost')
  || strlen($PGHOST) == 0 || preg_match('/^\//', $PGHOST) == 1) {
  if ($g_version > '80') {
    exec('pg_config', $lignes, $errorcode);
  } else {
    $options = array('bindir', 'includedir', 'includedir-server',
                     'libdir', 'pkglibdir', 'configure', 'pgxs');
    for ($i = 0; $i < count($options) or $errorcode != 0; $i++) {
      if ($options[$i] != 'pgxs' || $g_version == '80') {
        unset($tmp);
        exec('pg_config --'.$options[$i], $tmp, $errorcode);
        $lignes[$i] = strtoupper($options[$i]).' = '.$tmp[0];
      }
    }
  }

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

    for ($index = 0; $index < count($lignes); $index++) {
      $ligne = split('=', $lignes[$index], 2);
      $buffer .= tr().'
  <td>'.$ligne[0].'</td>
  <td>'.$ligne[1].'</td>
</tr>';
    }
    $buffer .= '</tbody>
</table>
</div>
';
  } else {
    $buffer .= '<div class="warning">pg_config returns error code '.$errorcode.'!</div>';
  }
} else {
  $buffer .= '<div class="warning">Remote execution, so pg_config results unavailable!</div>';
}

$filename = $outputdir.'/pgconfig.html';
include 'lib/fileoperations.php';

?>
