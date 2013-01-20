<?php

/*
 * Copyright (c) 2008-2013 Guillaume Lelarge <guillaume@lelarge.info>
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

if (!function_exists('pg_connect')) {
  $msg = "pg_connect() function is not available!\n
You probably need to install PHP PostgreSQL driver.\n";
  die($msg);
}

// password checks
if ($g_passwordrequired && strlen($PGPASSWORD) == 0) {
  // check pgpass first
  $pgpassfilename = getenv('HOME').'/.pgpass';
  if (file_exists($pgpassfilename)) {
    $permissions = substr(decoct(fileperms($pgpassfilename)), 3);
    if (!strcmp($permissions, '600')) {
      $pgpassfile = fopen($pgpassfilename, 'r');
      $found = false;
      while (!$found && $line = fgets($pgpassfile)) {
        list($host, $port, $database, $user, $password) = split (":", trim($line), 5);
        if ((!strcmp($PGHOST, $host) || !strcmp('*', $host)) &&
            (!strcmp($PGPORT, $port) || !strcmp('*', $port)) &&
            (!strcmp($PGDATABASE, $database) || !strcmp('*', $database)) &&
            (!strcmp($PGUSER, $user) || !strcmp('*', $user))) {
          $found = true;
          $PGPASSWORD = $password;
        }
      }
    }
  }
  // if still no password
  if (!$g_nopassword && strlen($PGPASSWORD) == 0) {
    // Be careful, password will appear in clear text on the terminal...
    // At least, it won't show up in ps output :-/
    echo "Password: ";
    $stdin = fopen('php://stdin', 'r');
    $PGPASSWORD = fgets(STDIN);
  }
}

// Connects to database via the usual environnement variables
// actually, connects to a specific one

$DSN = '';

if (strlen("$PGHOST") > 0) {
  $DSN .= 'host='.$PGHOST.' ';       
}
if (strlen("$PGPORT") > 0) {
  $DSN .= 'port='.$PGPORT.' ';       
}

$DSN .= 'dbname='.$PGDATABASE.' '.
       'user='.$PGUSER;

if (!$g_witholdlibpq) {
  $DSN .= ' application_name=pgsnap';
}

if (strlen("$PGPASSWORD") > 0) {
  $DSN .= ' password='.$PGPASSWORD;
}

$connection = @pg_connect($DSN);

if (!$connection) {
  echo "Connection error !\n";
  echo "DSN is $DSN\n";
  if (!$g_witholdlibpq) {
    echo "Maybe you should try with --with-old-libpq option if you don't have";
    echo " an uptodate libpq.\n";
  }
  die();
}

?>
