<?php

/*
 * Copyright (c) 2008-2010 Guillaume Lelarge <guillaume@lelarge.info>
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

<h1>Non Default Configuration</h1>

<p><b>Be careful</b>, this is not the result from reading the
postgresql.conf file, but it\'s the actual configuration available
when connecting to <b>database '.$PGDATABASE.' on server '.$PGHOST.
':'.$PGPORT.' as user '.$PGUSER.'</b>.</p>';

$cat1 = array();

if ($g_version > 74) {
  $query_cat = "SELECT DISTINCT category AS name FROM pg_settings";
  $categories = pg_query($connection, $query_cat);
  if (!$categories) {
    echo "An error occured.\n";
    exit;
  }
  $buffer2 = '';
  while ($categorie = pg_fetch_array($categories)) {
    $cat1[] = $categorie['name'];
    $cat2[] = preg_replace('/[ \/]/', '', $categorie['name']);
    if (strlen($buffer2) > 0) {
      $buffer2 .= ', ';
    }
    $buffer2 .= '<a href="#'.$cat2[count($cat2)-1].'">'.$cat1[count($cat1)-1].'</a>';
  }
  $buffer .= '<p>'.$buffer2.'</p>';
  pg_free_result($categories);
}

$index = 0;
$done = $index >= count($cat1);

while (!$done) {
  if ($g_version == 74 || $categories) {
    $query = "SELECT name, setting, ";
    if ($g_version >= 82) {
      $query .= "unit, ";
    }
    if ($g_version > 74) {
      $query .= "short_desc, extra_desc, ";
    }
    $query .= "context, vartype, source, min_val, max_val
      FROM pg_settings 
      WHERE source <> 'default' ";
    if ($categories) {
      $query .= "AND category='".$cat1[$index]."' ";
      $buffer .= '<h3 id="'.$cat2[$index].'">'.$cat1[$index].'</h3>';
    }
    $query .= "ORDER BY name";
  }

  $rows = pg_query($connection, $query);
  if (!$rows) {
    echo "An error occured.\n";
    exit;
  }

  if (pg_num_rows($rows) > 0) {
    $buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Name</th>
  <th class="colMid">Actual setting</th>
  <th class="colMid">Context</th>
  <th class="colMid">Vartype</th>
  <th class="colMid">Source</th>
  <th class="colLast">Min/Max value</th>
</tr>
';

    while ($row = pg_fetch_array($rows)) {
      $buffer .= tr().'
  <td title="'.preg_replace('/"/', "'", $row['short_desc'])."\n".
  preg_replace('/"/', "'", $row['extra_desc']).'">'.$row['name']."</td>
  <td>".$row['setting'];
      if ($g_version > 82) {
        $buffer .= ' '.$row['unit'];
      }
      $buffer .= "</td>
  <td>".$row['context']."</td>
  <td>".$row['vartype']."</td>
  <td>".$row['source']."</td>
  <td>";
      if (strlen($row['min_val']) > 0) {
        $buffer .= $row['min_val'];
      }
      if (strlen($row['min_val']) > 0 || strlen($row['max_val']) > 0) {
        $buffer .= '/';
      }
      if (strlen($row['max_val']) > 0) {
        $buffer .= $row['max_val'];
      }
      $buffer .= "</td>
</tr>";
    }
    $buffer .= '</table>
</div>
';
  }

  if ($g_version == 74) {
    $done = true;
  } else {
    $done = $index >= count($cat1) - 1;
  }

  $index++;
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/nondefaultparam.html';
include 'lib/fileoperations.php';

?>
