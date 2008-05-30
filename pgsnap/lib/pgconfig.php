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

$buffer = $navigate_general.'
<div id="pgContentWrap">

<h1>pg_config</h1>

';

if (!strcmp($PGHOST, '127.0.0.1') or !strcmp($PGHOST, 'localhost')
  or strlen($PGHOST) == 0 or preg_match('/^\//', $PGHOST) == 1) {
  if ($g_version > '80') {
    exec('pg_config', $lignes);
  } else {
    exec('pg_config --bindir', $lignes[0]);
    $lignes[0] = 'BINDIR = '.$lignes[0];
    exec('pg_config --includedir', $lignes[1]);
    $lignes[1] = 'INCLUDEDIR = '.$lignes[1];
    exec('pg_config --includedir-server', $lignes[2]);
    $lignes[2] = 'INCLUDEDIR-SERVER = '.$lignes[2];
    exec('pg_config --libdir', $lignes[3]);
    $lignes[3] = 'LIBDIR = '.$lignes[3];
    exec('pg_config --pkglibdir', $lignes[4]);
    $lignes[4] = 'PKGLIBDIR = '.$lignes[4];
    exec('pg_config --pgxs', $lignes[5]);
    $lignes[5] = 'PGXS = '.$lignes[5];
    exec('pg_config --configure', $lignes[6]);
    $lignes[6] = 'CONFIGURE = '.$lignes[6];
  }

  $buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst" width="30%">Variable</th>
  <th class="colLast" width="70%">Value</th>
</tr>
';

  for ($index = 0; $index < count($lignes); $index++) {
    $ligne = split('=', $lignes[$index], 2);
    $buffer .= tr().'
  <td>'.$ligne[0].'</td>
  <td>'.$ligne[1].'</td>
</tr>';
  }
  $buffer .= '</table>
</div>
';
} else {
  $buffer .= '<div class="warning">Remote execution, so pg_config results unavailable!</div>';
}

$filename = $outputdir.'/pgconfig.html';
include 'lib/fileoperations.php';

?>
