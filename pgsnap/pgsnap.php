#! /usr/bin/php -qC
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

include 'lib/getopt.php';

// if the directory doesn't exist, we create it
if (file_exists($outputdir)) {
  $files = scandir($outputdir);
  if (count($files) > 2) {
    die ("Directory $outputdir already here and not empty!\n");
  }
} else {
  mkdir($outputdir);
}

echo "Connecting...\n";
include 'lib/connect.php';

$query = "SHOW server_version";
$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
if ($row = pg_fetch_array($rows)) {
  $tmp = split("\.", $row['server_version']);
  $g_version = $tmp[0].$tmp[1];
}

echo "Adding some HTML files...\n";
copy('template/index.html', $outputdir.'/index.html');
copy('template/screenstyle.css', $outputdir.'/screenstyle.css');
copy('images/check-off.png', $outputdir.'/check-off.png');
copy('images/check-on.png', $outputdir.'/check-on.png');
$image['f'] = '<img src="check-off.png" title="Off" alt="Off"/>';
$image['t'] = '<img src="check-on.png" title="On" alt="On"/>';

echo "Getting Misc informations...\n";
include 'lib/navigate.php';
include 'lib/links.php';

echo "Getting General informations...\n";
include 'lib/ver.php';
include 'lib/sessionsinfo.php';
if ($g_version > '74') {
  include 'lib/pgconfig.php';
}
include 'lib/pgcontroldata.php';
include 'lib/param.php';
if ($g_version > '74') {
  include 'lib/paramautovac.php';
}

echo "Getting Global Informations...\n";
include 'lib/bases.php';
include 'lib/databasesincache.php';
include 'lib/roles.php';
include 'lib/user1.php';
include 'lib/user2.php';
include 'lib/tablespaces.php';
include 'lib/tblspc1.php';

echo "Getting Database Informations...\n";
include 'lib/schemas.php';
include 'lib/tables.php';
include 'lib/tablesincache.php';
include 'lib/tableswithoutpkey.php';
include 'lib/tableswith5+indexes.php';
include 'lib/fkconstraints.php';
include 'lib/views.php';
include 'lib/sequences.php';
include 'lib/indexes.php';
include 'lib/languages.php';

echo "Getting Current Activities Informations...\n";
include 'lib/activities.php';
include 'lib/locks.php';
if ($g_version >= '82') {
  include 'lib/cursors.php';
  include 'lib/preparedstatements.php';
  include 'lib/preparedxacts.php';
}

echo "Getting Statistical Informations...\n";
if ($g_version == '83') {
  include 'lib/bgwriter.php';
}
include 'lib/stat_databases.php';
include 'lib/stat_tables.php';
include 'lib/lastvacuumtables.php';
include 'lib/lastanalyzetables.php';
include 'lib/stat_indexes.php';

echo "Getting Tools Informations...\n";
include 'lib/pgpool.php';

pg_close($connection);

/*
en fait, j'aime pas trop la fa�on dont c'est partitionn�
il faut trouver un meilleur classement
on pourrait faire
 * G�n�ral
   * produits install�s
   * pg_config (et indiquer la version du cluster)
   * configuration de PG
 * Syst�me d'exploitation
   * Syst�me install� (distrib linux)
   * Processus (sp�cifiques PostgreSQL + Slony + Pgpool + ?)
 * M�moire
   * RAM (quantit� utilis�, libre)
   * pg_buffercache
   * pg_freespacemap
 * Syst�me disque
   * Disques (nom + espace utilis� + pourcentage libre)
   * Partitions (nom + espace utilis� + pourcentage libre)
   * Bases de donn�es (nom + espace utilis�)
   * Tablespaces (nom + espace utilis� + pourcentage libre)
   * Tables (nom + espace utilis� + pgstattuple)
   * Index (nom + espace utilis� + pgstatindex)
   * XLOG (nombre, taille du r�pertoire, nombre de ready)
   * Log applicatifs
 * Database
   * Liste (pg_database)
   * % base de donn�es en m�moire (si pg_buffercache)
 * Sch�ma
 * Tables
   * Liste (pg_database)
   * 
 * Index
 * Statistiques
 * Pgpool
 * Pgpool II
 * pgBouncer
 * Slony
   
*/

?>
