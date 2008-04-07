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

$buffer = "<h1>Sessions</h1>";

$query = "SELECT name, setting FROM pg_settings
  WHERE name IN ('max_connections', 'autovacuum_max_workers');";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $$row['name'] = $row['setting'];
}

$buffer .= "<tr>
  <td>".$row['product']."</td>
  <td>".$row['version']."</td>
</tr>";

$query = "SELECT COUNT(*) FROM pg_stat_activity";

$rows = @pg_query($connection, $query);
if ($rows) {
  $row = pg_fetch_array($rows);
  $count = $row[0];
}

$buffer .= "<table>
<thead>
<tr>
  <td>Actual sessions</td>
  <td>Max connections</td>
  <td>Autovac Workers</td>
</tr>
</thead>
<tbody>
<tr>
  <td>";

if ($count > $max_connections*90/100) {
  $buffer .= '<span id="danger">'.$count.'</div>';
} else {
  $buffer .= $count;
}

$buffer .= "</td>
  <td>".$max_connections."</td>
  <td>".$autovacuum_max_workers."</td>
</tr>
</tbody>
</table>\n";

$filename = $outputdir.'/sessionsinfo.html';
include 'lib/fileoperations.php';

?>
