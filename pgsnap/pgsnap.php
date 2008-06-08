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

/****************************************************************************/
/* This can be modified to suit your needs                                  */
$PGSNAP_ROOT_PATH='./';
ini_set('include_path', '.:/usr/share/pgsnap/');
/****************************************************************************/

include 'lib/getopt.php';
include 'lib/ui.php';

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
// javascript
copy($PGSNAP_ROOT_PATH.'external/jquery-1.2.3.js',
     $outputdir.'/jquery-1.2.3.js');
// css
copy($PGSNAP_ROOT_PATH.'template/fixed.css', $outputdir.'/fixed.css');
copy($PGSNAP_ROOT_PATH.'template/global.css', $outputdir.'/global.css');
copy($PGSNAP_ROOT_PATH.'template/layout.css', $outputdir.'/layout.css');
copy($PGSNAP_ROOT_PATH.'template/text.css', $outputdir.'/text.css');
copy($PGSNAP_ROOT_PATH.'template/navigation.css', $outputdir.'/navigation.css');
copy($PGSNAP_ROOT_PATH.'template/table.css', $outputdir.'/table.css');
copy($PGSNAP_ROOT_PATH.'template/iefixes.css', $outputdir.'/iefixes.css');
// png
copy($PGSNAP_ROOT_PATH.'images/check-off.png', $outputdir.'/check-off.png');
copy($PGSNAP_ROOT_PATH.'images/check-on.png', $outputdir.'/check-on.png');
copy($PGSNAP_ROOT_PATH.'images/danger.png', $outputdir.'/danger.png');
copy($PGSNAP_ROOT_PATH.'images/important.png', $outputdir.'/important.png');
copy($PGSNAP_ROOT_PATH.'images/tip.png', $outputdir.'/tip.png');
copy($PGSNAP_ROOT_PATH.'images/warning.png', $outputdir.'/warning.png');

copy($PGSNAP_ROOT_PATH.'images/hdr_left.png', $outputdir.'/hdr_left.png');
copy($PGSNAP_ROOT_PATH.'images/hdr_right.png', $outputdir.'/hdr_right.png');
copy($PGSNAP_ROOT_PATH.'images/bg_hdr.png', $outputdir.'/bg_hdr.png');
copy($PGSNAP_ROOT_PATH.'images/blt_blu_arrow.png', $outputdir.'/blt_blu_arrow.png');
copy($PGSNAP_ROOT_PATH.'images/blt_gry_arrow.png', $outputdir.'/blt_gry_arrow.png');
copy($PGSNAP_ROOT_PATH.'images/box_bottom.gif', $outputdir.'/box_bottom.gif');
copy($PGSNAP_ROOT_PATH.'images/box_top.gif', $outputdir.'/box_top.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_bl.gif', $outputdir.'/feature_bl.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_br.gif', $outputdir.'/feature_br.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_tl.gif', $outputdir.'/feature_tl.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_tr.gif', $outputdir.'/feature_tr.gif');
copy($PGSNAP_ROOT_PATH.'images/hdr_fill.png', $outputdir.'/hdr_fill.png');
copy($PGSNAP_ROOT_PATH.'images/nav_fill.png', $outputdir.'/nav_fill.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm_lft.png', $outputdir.'/nav_tbl_btm_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm.png', $outputdir.'/nav_tbl_btm.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm_rgt.png', $outputdir.'/nav_tbl_btm_rgt.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top_lft.png', $outputdir.'/nav_tbl_top_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top.png', $outputdir.'/nav_tbl_top.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top_rgt.png', $outputdir.'/nav_tbl_top_rgt.png');
copy($PGSNAP_ROOT_PATH.'images/usr_tbl_btm.png', $outputdir.'/usr_tbl_btm.png');
copy($PGSNAP_ROOT_PATH.'images/usr_tbl_top.png', $outputdir.'/usr_tbl_top.png');
copy($PGSNAP_ROOT_PATH.'images/nav_lft.png', $outputdir.'/nav_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_rgt.png', $outputdir.'/nav_rgt.png');
// flash stuff
if (file_exists('external/open-flash-chart.swf')
    and file_exists('external/swfobject.js')) {
  mkdir($outputdir.'/js');
  copy($PGSNAP_ROOT_PATH.'external/open-flash-chart.swf', $outputdir.'/open-flash-chart.swf');
  copy($PGSNAP_ROOT_PATH.'external/swfobject.js', $outputdir.'/js/swfobject.js');
}
// variables
$image['f'] = '<img src="check-off.png" title="Off" alt="Off"/>';
$image['t'] = '<img src="check-on.png" title="On" alt="On"/>';
$image['off'] = '<img src="check-off.png" title="Off" alt="Off"/>';
$image['on'] = '<img src="check-on.png" title="On" alt="On"/>';

echo "Getting Misc informations...\n";
include 'lib/getmodules.php';
include 'lib/navigate.php';
include 'lib/links.php';

echo "Getting General informations...\n";
include 'lib/ver.php';
include 'lib/sessionsinfo.php';
include 'lib/pgconfig.php';
include 'lib/pgcontroldata.php';
include 'lib/param.php';
if ($g_version > '80') {
  include 'lib/paramautovac.php';
}
if ($g_version > '74'
    and (!strcmp($g_settings['log_destination'], 'stderr')
         or !strcmp($g_settings['log_destination'], 'csvlog'))
    and ((array_key_exists('redirect_stderr', $g_settings)
           and !strcmp($g_settings['redirect_stderr'], 'on'))
         or (array_key_exists('logging_collector', $g_settings)
           and!strcmp($g_settings['logging_collector'], 'on'))) ) {
  include 'lib/lastlogfile.php';
}

echo "Getting Global Informations...\n";
include 'lib/bases.php';
if ($g_flashexists and $g_version > '80') {
  include 'lib/graph_dbsize.php';
}
if ($g_pgbuffercache) {
  include 'lib/databasesincache.php';
} else {
  echo "  pg_buffercache unavailable!\n";
}
if ($g_version > '80') {
  include 'lib/roles.php';
} else {
  include 'lib/users.php';
}
include 'lib/user1.php';
include 'lib/user2.php';
if ($g_version > '74') {
  include 'lib/tablespaces.php';
  if ($g_flashexists and $g_version > '80') {
    include 'lib/graph_tblspcsize.php';
  }
  include 'lib/tblspc1.php';
}

echo "Getting Database Informations...\n";
include 'lib/schemas.php';
include 'lib/tables.php';
if ($g_flashexists and $g_version > '80') {
  include 'lib/graph_tablesize.php';
}
if ($g_pgbuffercache) {
  include 'lib/tablesincache.php';
} else {
  echo "  pg_buffercache unavailable!\n";
}
if ($g_pgstattuple) {
  include 'lib/fragmentedtables.php';
} else {
  echo "  pgstattuple unavailable!\n";
}
include 'lib/tableswithoutpkey.php';
include 'lib/tableswith5+indexes.php';
include 'lib/fkconstraints.php';
include 'lib/views.php';
include 'lib/sequences.php';
include 'lib/indexes.php';
if ($g_pgstatindex) {
  include 'lib/fragmentedindexes.php';
} else {
  echo "  pgstattuple on indexes unavailable!\n";
}
include 'lib/relationsbloat.php';
include 'lib/languages.php';
include 'lib/functions.php';

echo "Getting Current Activities Informations...\n";
include 'lib/activities.php';
include 'lib/nonidleprocesses.php';
include 'lib/locks.php';
if ($g_version > '80') {
  include 'lib/exclusivelocks.php';
}
if ($g_version >= '82') {
  include 'lib/cursors.php';
  include 'lib/preparedstatements.php';
  include 'lib/preparedxacts.php';
}

echo "Getting Statistical Informations...\n";
if ($g_version == '83') {
  include 'lib/bgwriter.php';
}
include 'lib/cachehitratio.php';
include 'lib/stat_databases.php';
include 'lib/stat_tables.php';
if ($g_version >= '82') {
  include 'lib/lastvacuumtables.php';
  include 'lib/lastanalyzetables.php';
}
include 'lib/stat_indexes.php';
if ($g_fsmrelations) {
  include 'lib/fsmrelations.php';
}
if ($g_fsmpages) {
  include 'lib/fsmpages.php';
}

echo "Getting Tools Informations...\n";
if ($g_pgpool) {
  include 'lib/pgpool.php';
} else {
  echo "  pgPool unavailable!\n";
}

pg_close($connection);

?>
