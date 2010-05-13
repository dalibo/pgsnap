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

// Comments on Databases
$query = 'SELECT datname, description
FROM pg_database
LEFT JOIN ';
if ($g_version > 81)
  $query .= 'pg_shdescription';
else
  $query .= 'pg_description';
$query .= ' ON pg_database.oid=objoid
ORDER BY datname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['databases'][$row['datname']] = $row['description'];
 }

// Comments on Tablespaces
if ($g_version > 74) {
  if ($g_version > 81) {
    $query = "SELECT spcname,
  pg_catalog.shobj_description(pg_tablespace.oid, 'pg_tablespace') AS description
FROM pg_tablespace
ORDER BY spcname";
  } else {
    $query = "SELECT spcname, description
FROM pg_tablespace
LEFT JOIN pg_description ON pg_tablespace.oid=objoid
ORDER BY spcname";
  }

  $rows = pg_query($connection, $query);
  if (!$rows) {
    echo "An error occured.\n";
    exit;
  }

  while ($row = pg_fetch_array($rows)) {
    $comments['tablespaces'][$row['spcname']] = $row['description'];
  }
}

// Comments on Roles
if ($g_version > 81) {
$query = "SELECT rolname,
  pg_catalog.shobj_description(pg_roles.oid, 'pg_authid') AS description
FROM pg_roles
ORDER BY rolname";
} elseif ($g_version > 80) {
$query = "SELECT rolname, description
FROM pg_roles
LEFT JOIN pg_description ON pg_roles.oid=objoid
ORDER BY rolname";
} else {
$query = "SELECT usename AS rolname,
  '' as description
FROM pg_shadow
ORDER BY usename";
}

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['roles'][$row['rolname']] = $row['description'];
 }

// Comments on Languages
$query = 'SELECT lanname, description
FROM pg_language
LEFT JOIN pg_description ON pg_language.oid=objoid
ORDER BY lanname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['languages'][$row['lanname']] = $row['description'];
 }

// Comments on Schemas
$query = 'SELECT nspname, description
FROM pg_namespace
LEFT JOIN pg_description ON pg_namespace.oid=objoid
ORDER BY nspname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['schemas'][$row['nspname']] = $row['description'];
 }

// Comments on Relations (tables, views, sequences, indexes)
$query = 'SELECT nspname, relname, description
FROM pg_class
LEFT JOIN pg_namespace ON pg_namespace.oid=relnamespace
LEFT JOIN pg_description ON pg_class.oid=objoid
ORDER BY nspname, relname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['relations'][$row['nspname']][$row['relname']] = $row['description'];
 }

// Comments on Functions
$query = 'SELECT nspname, proname, description
FROM pg_proc
LEFT JOIN pg_namespace ON pg_namespace.oid=pronamespace
LEFT JOIN pg_description ON pg_proc.oid=objoid
ORDER BY nspname, proname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

while ($row = pg_fetch_array($rows)) {
  $comments['functions'][$row['nspname']][$row['proname']] = $row['description'];
 }

?>
