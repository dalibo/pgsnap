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

<h1>Autovacuum Configuration</h1>
';

if ($g_version >= 82) {
  $query = "SELECT
  relname,
  enabled,
  vac_base_thresh,
  vac_scale_factor,
  anl_base_thresh,
  anl_scale_factor,
  vac_cost_delay,
  vac_cost_limit,
  freeze_min_age,
  freeze_max_age
FROM pg_class, pg_autovacuum
WHERE pg_class.oid = vacrelid
ORDER BY relname";
} else{
  $query = "SELECT
  relname,
  enabled,
  vac_base_thresh,
  vac_scale_factor,
  anl_base_thresh,
  anl_scale_factor,
  vac_cost_delay,
  vac_cost_limit
FROM pg_class, pg_autovacuum
WHERE pg_class.oid = vacrelid
ORDER BY relname";
}

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) == 0) {
  $buffer .= '<div class="warning">No specific configuration.</div>';
} else {
  $buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Table</th>
  <th class="colMid">Enabled?</th>
  <th class="colMid">Owner</th>
  <th class="colMid">Vacuum threshold</th>
  <th class="colMid">Vacuum scale factor</th>
  <th class="colMid">Analyze threshold</th>
  <th class="colMid">Analyze scale factor</th>
  <th class="colMid">Vacuum Cost Delay</th>
  <th class="colMid">Vacuum Cost Limit</th>';
if ($g_version > 82) {
  $buffer .= '
  <th class="colMid">Free Min Age</td>
  <th class="colLast">Freeze Max Age</td>
';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

  while ($row = pg_fetch_array($rows)) {
    $buffer .= tr().'
  <td>'.$row['relname'].'</td>
  <td>'.$row['enabled'].'</td>
  <td>'.$row['vac_base_thresh'].'</td>
  <td>'.$row['vac_scale_factor'].'</td>
  <td>'.$row['anl_base_thresh'].'</td>
  <td>'.$row['anl_scale_factor'].'</td>
  <td>'.$row['vac_cost_delay'].'</td>
  <td>'.$row['vac_cost_limit'].'</td>';
    if ($g_version > 82) {
      $buffer .= '
  <td>'.$row['freeze_min_age'].'</td>
  <td>'.$row['freeze_max_age'].'</td>';
    }
$buffer .= '
</tr>';
  }
  $buffer .= '</tbody>
</table>
</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/paramautovac.html';
include 'lib/fileoperations.php';

?>
