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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Functions</h1>
';

$buffer .= '<label><input id ="showusrobjects" type="checkbox" onclick="usrobjects();" checked>Show User Objects</label>';
$buffer .= '<label><input id ="showsysobjects" type="checkbox" onclick="sysobjects();" checked>Show System Objects</label>';

$query = "SELECT n.nspname,
  p.proname,
  CASE WHEN p.proretset THEN 'setof '
       ELSE '' END ||
    pg_catalog.format_type(p.prorettype, NULL) as returntype,
  CASE WHEN proallargtypes IS NOT NULL THEN
    pg_catalog.array_to_string(ARRAY(
      SELECT
        CASE
          WHEN p.proargmodes[s.i] = 'i' THEN ''
          WHEN p.proargmodes[s.i] = 'o' THEN 'OUT '
          WHEN p.proargmodes[s.i] = 'b' THEN 'INOUT '
        END ||
        CASE
          WHEN COALESCE(p.proargnames[s.i], '') = '' THEN ''
          ELSE p.proargnames[s.i] || ' '
        END ||
        pg_catalog.format_type(p.proallargtypes[s.i], NULL)
      FROM
        pg_catalog.generate_series(1, pg_catalog.array_upper(p.proallargtypes, 1)) AS s(i)
    ), ', ')
  ELSE
    pg_catalog.array_to_string(ARRAY(
      SELECT
        CASE
          WHEN COALESCE(p.proargnames[s.i+1], '') = '' THEN ''
          ELSE p.proargnames[s.i+1] || ' '
          END ||
        pg_catalog.format_type(p.proargtypes[s.i], NULL)
      FROM
        pg_catalog.generate_series(0, pg_catalog.array_upper(p.proargtypes, 1)) AS s(i)
    ), ', ')
  END AS args,
  CASE
    WHEN p.provolatile = 'i' THEN 'immutable'
    WHEN p.provolatile = 's' THEN 'stable'
    WHEN p.provolatile = 'v' THEN 'volatile'
  END as volatility,
  r.rolname,
  l.lanname
FROM pg_catalog.pg_proc p
     LEFT JOIN pg_catalog.pg_namespace n ON n.oid = p.pronamespace
     LEFT JOIN pg_catalog.pg_language l ON l.oid = p.prolang
     JOIN pg_catalog.pg_roles r ON r.oid = p.proowner
WHERE p.prorettype <> 'pg_catalog.cstring'::pg_catalog.regtype
      AND (p.proargtypes[0] IS NULL
      OR   p.proargtypes[0] <> 'pg_catalog.cstring'::pg_catalog.regtype)
      AND NOT p.proisagg
  AND pg_catalog.pg_function_is_visible(p.oid)
ORDER BY 1, 2, 3, 4";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Owner</th>
  <th class="colMid">Schema Name</th>
  <th class="colMid">Function Name</th>
  <th class="colMid">Return type</th>
  <th class="colMid">Args</th>
  <th class="colMid">Volatibility</th>
  <th class="colLast">Language</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td>".$row['rolname']."</td>
  <td>".$row['nspname']."</td>
  <td>".$row['proname']."</td>
  <td>".$row['returntype']."</td>
  <td>".$row['args']."</td>
  <td>".$row['volatility']."</td>
  <td>".$row['lanname']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/functions.html';
include 'lib/fileoperations.php';

?>
