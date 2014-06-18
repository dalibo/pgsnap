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

$VERSION = '0.8.0';

$PGHOST = getenv('PGHOST');

$PGPORT = getenv('PGPORT');

$PGUSER = getenv('PGUSER');
if (strlen("$PGUSER") == 0) {
  $PGUSER = getenv('USER');
}

$PGDATA = getenv('PGDATA');

$PGDATABASE = getenv('PGDATABASE');

$PGPASSWORD = getenv('PGPASSWORD');
$g_passwordrequired = false;
$g_nopassword = false;
$g_withoutsysobjects = false;
$g_alldatabases = false;
$g_deleteifexists = false;
$g_witholdlibpq = false;
$outputdir = '';
$outputdirmode = 700;
$queriesinlogs = false;

for ($i = 1; $i < $_SERVER["argc"]; $i++) {
  switch($_SERVER["argv"][$i]) {
    case "-v":
    case "--version":
      echo  $_SERVER['argv'][0]." $VERSION\n";
      exit;
      break;
    case "-h":
    case "--host":
      $PGHOST = $_SERVER['argv'][++$i];
      break;
    case "-p":
    case "--port":
      $PGPORT = $_SERVER['argv'][++$i];
      break;
    case "-U":
    case "--user":
      $PGUSER = $_SERVER['argv'][++$i];
      break;
    case "-d":
    case "--database":
      $PGDATABASE = $_SERVER['argv'][++$i];
      break;
    case "-w":
    case "--no-password":
      if ($g_passwordrequired) {
        die("-w and -W parameters are mutually exclusive.\n");
      }
      $g_nopassword = true;
      break;
    case "-W":
      if ($g_nopassword) {
        die("-w and -W parameters are mutually exclusive.\n");
      }
      $g_passwordrequired = true;
      break;
    case "--with-old-libpq":
      $g_witholdlibpq = true;
      break;
    case "-o":
    case "--output-dir":
      $outputdir = $_SERVER['argv'][++$i];
      break;
    case "--output-dir-mode":
      $outputdirmode = $_SERVER['argv'][++$i];
      break;
    case "-S":
    case "--without-sysobjects":
      $g_withoutsysobjects = true;
      break;
    case "-a":
    case "--all":
      $g_alldatabases = true;
      break;
    case "--delete-if-exists":
      $g_deleteifexists = true;
      break;
    case "--query-in-logs":
      $queriesinlogs = true;
      break;
    case "-?":
    case "-h":
    case "--help":
?>
This is <?php echo $_SERVER['argv'][0]; ?> <?php echo $VERSION ?>.

Usage:
  <?php echo $_SERVER['argv'][0]; ?> [OPTIONS]... [DBNAME]

General options:
  -a, --all       build a report for all databases on the PostgreSQL server
  -d DBNAME       specify database name to connect to
                  (default: "<?php echo $PGDATABASE ?>")
  --delete-if-exists
                  delete output directory if it already exists
  -o outputdir    specify output directory
                  (with -a, defaults to the current working directory)
                  (without -a, defaults to: "<?php echo $outputdir ?>")
  --output-dir-mode outputdirmode    specify output directory permissions mode
                  (default: "<?php echo $outputdirmode ?>")
  --query-in-logs allow logging queries in PostgreSQL log files
  --with-old-libpq
                  disable the use of the parameter application_name
  -S, --without-sysobjects
                  get report without system objects informations
  -?, --help          show this help, then exit
  -v, --version   output version information, then exit

Connection options:
  -h HOSTNAME     database server host or socket directory
                  (default: "<?php echo $PGHOST ?>")
  -p PORT         database server port (default: "<?php echo $PGPORT ?>")
  -U NAME         database user name (default: "<?php echo $PGUSER ?>")
  -W              prompt for password
  -w              don't prompt for password

<?php
      exit;
      break;
    default:
      $PGDATABASE = $_SERVER['argv'][$i];
      break;
  }
}

if (!$g_alldatabases && strlen("$PGDATABASE") == 0) {
  $PGDATABASE = $PGUSER;
}
?>
