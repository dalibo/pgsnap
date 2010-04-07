#! /usr/bin/php -qC
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

/****************************************************************************/
/* This can be modified to suit your needs                                  */
$PGSNAP_ROOT_PATH='./';
ini_set('include_path', '.:/usr/share/pgsnap/');
/****************************************************************************/

include 'lib/getopt.php';
include 'lib/ui.php';

if ($g_alldatabases) {
  // Fetching databases' names
  $PGDATABASE = 'template1';
  echo "Connecting...\n";
  include 'lib/connect.php';

  $query = "SELECT datname FROM pg_database WHERE datallowconn IS TRUE";
  $rows = pg_query($connection, $query);
  if (!$rows) {
    echo "An error occured.\n";
    exit;
  }
  while ($row = pg_fetch_array($rows)) {
    $databases[] = $row['datname'];
  }
  pg_close($connection);
} else {
  $databases[] = $PGDATABASE;
}

// Building a report for each database in $databases
foreach ($databases as $PGDATABASE) {
  include 'lib/reports.php';
}

?>
