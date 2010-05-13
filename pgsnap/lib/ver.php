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

<h1>Installed products</h1>

<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Product</th>
  <th class="colLast">Comments</th>
</tr>
';

$query = "SELECT 'PostgreSQL' AS product, version() AS version";
if ($g_version >= 81) {
    $query .= ", pg_postmaster_start_time() AS starttime, ";
    if ($g_version >= 84) {
	    $query .= "pg_conf_load_time()";
    } else {
	    $query .= "'unavailable'";
    }
    $query .= " AS reloadtime";
    if ($g_version >= 90) {
		$query .= ", pg_is_in_recovery() AS recovery,
  pg_last_xlog_receive_location(), pg_last_xlog_replay_location() ";
	}
}

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['product'].'</td>
  <td>'.$row['version'].'</td>
</tr>
';
}

if ($g_pgpool) {
  $buffer .= tr().'
  <td>pgPool</td>
  <td><i>Tool</i></td>
</tr>
';
}
if ($g_pgbuffercache) {
  $buffer .= tr().'
  <td>pg_buffercache</td>
  <td><i>Contrib module</i></td>
</tr>
';
}
if ($g_pgstattuple) {
  $buffer .= tr().'
  <td>pgstattuple</td>
  <td><i>Contrib module</i></td>
</tr>
';
}
if ($g_fsmrelations) {
  $buffer .= tr().'
  <td>pg_freespacemap</td>
  <td><i>Contrib module</i></td>
</tr>
';
}

// check a few other contrib modules by functions
$functions = array(
    'adminpack'          => 'pg_file_write',
    'btree_gin'          => 'gin_btree_consistent',
    'btree_gist'         => 'gbtreekey4_in',
    'citext'             => 'citext_smaller',
    'cube'               => 'cube_distance',
    'dblink'             => 'dblink_connect',
    'dict_int'           => 'dintdict_lexize',
    'dict_xsyn'          => 'dxsyn_lexize',
    'earthdistance'      => 'earth_distance',
    'fuzzystrmatch'      => 'dmetaphone_alt',
    'hstore'             => 'hstore_out',
    'intarray'           => 'intarray_del_elem',
    'isn'                => 'isbn13_in',
    'lo'                 => 'lo_manage',
    'ltree'              => 'ltree2text',
    'pageinspect'        => 'heap_page_items',
    'pgcrypto'           => 'gen_salt',
    'pg_row_locks'       => 'pgrowlocks',
    'pg_stat_statements' => 'pg_stat_statements_reset',
    'pg_trgm'            => 'show_trgm',
    'seg'                => 'seg_over_left',
    'sslinfo'            => 'ssl_client_cert_present',
    'tablefunc'          => 'crosstab4',
    'tsearch2'           => 'to_tsvector',
    'uuid'               => 'uuid_ns_x500',
    'xml2'               => 'xslt_process'
);

foreach ($functions as $module => $function) {
  $fquery = "SELECT 1 FROM pg_proc WHERE proname = '$function'";
  $frows = pg_query($connection, $fquery);
  if (!$frows) {
    echo "An error occured.\n";
    exit;
  }

  if (pg_num_rows($frows) == 1) {
    $buffer .= tr().'
  <td>'.$module.'</td>
  <td><i>Contrib module</i></td>
</tr>
';
  }
  pg_free_result($frows);
}

$buffer .= '</table>
';

if ($g_version >= 81) {
  $buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Started on</th>
  <th class="colLast">Conf. reloaded on</th>
</tr>
<tr>
  <td>'.$row['starttime'].'</td>
  <td>'.$row['reloadtime'].'</td>
</tr>
</table>
</div>
';
}

$buffer .= '<h1>Primary options</h1>
<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Option</th>
  <th class="colLast">Value</th>
</tr>
';

if ($g_version >= 90) {
  $buffer .= tr().'
  <td><a href="param.html#Write-AheadLogSettings">In Recovery</a></td>
  <td>'.$image[$row['recovery']];
  if ($row['recovery'] == 't') {
	  $buffer .= ' ('.$row['pg_last_xlog_receive_location'].' / '.$row['pg_last_xlog_replay_location'].')';
  }
  $buffer .= '</td>
</tr>
';
}

if (array_key_exists('autovacuum', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#Autovacuum">Autovacuum</a></td>
  <td>'.$image[$g_settings['autovacuum']].'</td>
</tr>
';
}

if (array_key_exists('track_activities', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#StatisticsQueryandIndexStatisticsCollector">Stats collector</a></td>
  <td>'.$image[$g_settings['track_activities']].'</td>
</tr>
';
} elseif (array_key_exists('stats_start_collector', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#StatisticsQueryandIndexStatisticsCollector">Stats collector</a></td>
  <td>'.$image[$g_settings['stats_start_collector']].'</td>
</tr>
';
} else {
  $buffer .= tr().'
  <td><a href="param.html#StatisticsQueryandIndexStatisticsCollector">Stats collector</a></td>
  <td>'.$image['off'].'</td>
</tr>
';
}
if (array_key_exists('logging_collector', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#ReportingandLoggingWheretoLog">Logging collector</a></td>
  <td>'.$image[$g_settings['logging_collector']].'</td>
</tr>
';
} elseif (array_key_exists('redirect_stderr', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#ReportingandLoggingWheretoLog">Logging collector</a></td>
  <td>'.$image[$g_settings['redirect_stderr']].'</td>
</tr>
';
} else {
  $buffer .= tr().'
  <td><a href="param.html#ReportingandLoggingWheretoLog">Logging collector</a></td>
  <td>'.$image['off'].'</td>
</tr>
';
}
if (array_key_exists('archive_mode', $g_settings)) {
  $buffer .= tr().'
  <td><a href="param.html#Write-AheadLogSettings">PITR</a></td>
  <td>'.$image[$g_settings['archive_mode']].'</td>
</tr>
';
} elseif (array_key_exists('archive_command', $g_settings) &&
  strlen($g_settings['archive_command'])>0) {
  $buffer .= tr().'
  <td><a href="param.html#Write-AheadLogSettings">PITR</a></td>
  <td>'.$image[strcmp($g_settings['archive_command'], 'unset') == 0 ? 'f':'t'].'</td>
</tr>
';
} else {
  $buffer .= tr().'
  <td><a href="param.html#Write-AheadLogSettings">PITR</a></td>
  <td>'.$image['off'].'</td>
</tr>
';
}
$buffer .= tr().'
  <td><a href="param.html#QueryTuningGeneticQueryOptimizer">GEQO</a></td>
  <td>'.$image[$g_settings['geqo']].'</td>
</tr>
';
$buffer .= tr().'
  <td><a href="param.html#ClientConnectionDefaultsLocaleandFormatting">Server encoding</a></td>
  <td>'.$g_settings['server_encoding'].'</td>
</tr>
';
$buffer .= tr().'
  <td><a href="param.html#ClientConnectionDefaultsLocaleandFormatting">Timezone</a></td>
  <td>'.$g_settings['TimeZone'].'</td>
</tr>
';
$buffer .= '</table>
</div>
';

$filename = $outputdir.'/ver.html';
include 'lib/fileoperations.php';

?>
