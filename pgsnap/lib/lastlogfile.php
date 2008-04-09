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

$buffer = "<h1>Contents of the last log file</h1>";

$query = "SELECT * FROM (
  SELECT
    pg_ls_dir('".pg_escape_string($g_settings['log_directory'])."'))
    AS tmp (filename)";
if (!strcmp($g_settings['log_destination'], 'csvlog')) {
  $query .= "WHERE filename LIKE '%csv' ";
}
$query .= "ORDER BY 1 DESC LIMIT 1";
$queries = $query." -- to get the latest filename<br/>\n";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
  $filename = $row['filename'];
}

$query = "SELECT size
FROM pg_stat_file('".
  pg_escape_string($g_settings['log_directory'].'/'.$filename).
  "')";
$queries .= $query." -- to get the size of the latest filename<br/>\n";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
  $size = $row['size'];
}

$query = "SELECT pg_read_file('".
  pg_escape_string($g_settings['log_directory'].'/'.$filename)."',
  0, ".$size.") as contents";
$queries .= $query." -- to get the contents of the latest filename<br/>\n";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
  $contents = $row['contents'];
}

$buffer .= '<pre>'.$contents.'</pre>';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$queries.'</p>
</div>';

$filename = $outputdir.'/lastlogfile.html';
include 'lib/fileoperations.php';

?>
