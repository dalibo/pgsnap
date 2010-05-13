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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Sequences - Metadata</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

// Build the SQL for the real query
$query = "SELECT nspname AS schema,
  '\"'||nspname||'\".\"'||relname||'\"' AS relname
FROM pg_class, pg_namespace
WHERE relkind='S'
  AND relnamespace=pg_namespace.oid
ORDER BY relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0)
{
  $query = '';
  while ($row = pg_fetch_array($rows)) {
    if (strlen($query) > 0)
      $query .= "\nUNION\n";
    $query .= "SELECT '".$row['schema']."' AS schema, sequence_name, last_value, ";
    if ($g_version > 83) {
      $query .= 'start_value, ';
    }
    $query .= 'increment_by,
    max_value, min_value, cache_value, log_cnt,
    is_cycled, is_called
  FROM '.$row['relname'];
  }
  $query .= ' ORDER BY sequence_name';
  
  $rows = pg_query($connection, $query);
  if (!$rows) {
    echo "An error occured.\n";
    exit;
  }
  
  $buffer .= '<div class="tblBasic">
  
  <table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
  <tr>
    <th class="colFirst">Schema name</th>
    <th class="colMid">Sequence name</th>
    <th class="colMid">Last Value</th>
    <th class="colMid">Start Value Owner</th>
    <th class="colMid">Increment By</th>
    <th class="colMid">Max Value</th>
    <th class="colMid">Min Value</th>
    <th class="colMid">Cache Value</th>
    <th class="colMid">Log Count</th>
    <th class="colMid">Is Cycled?</th>
    <th class="colLast">Is Called?</th>
  </tr>
  ';
  
  while ($row = pg_fetch_array($rows)) {
  $buffer .= tr($row['schema'])."
    <td title=\"".$comments['schemas'][$row['schema']]."\">".$row['schema']."</td>
    <td title=\"".$comments['relations'][$row['schema']][$row['sequence_name']]."\">".$row['sequence_name']."</td>
    <td>".$row['last_value']."</td>
    <td>".$row['start_value']."</td>
    <td>".$row['increment_by']."</td>
    <td>".$row['max_value']."</td>
    <td>".$row['min_value']."</td>
    <td>".$row['cache_value']."</td>
    <td>".$row['log_cnt']."</td>
    <td>".$image[$row['is_cycled']]."</td>
    <td>".$image[$row['is_called']]."</td>
  </tr>";
  }
  $buffer .= '</table>
  </div>
  ';
}
else
{
    $buffer .= "No existing sequences.";
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/sequences2.html';
include 'lib/fileoperations.php';

?>
