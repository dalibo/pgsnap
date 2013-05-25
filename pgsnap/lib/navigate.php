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

// General Side Nav

$navigate_header = '<div id="pgSideWrap">
  <div id="pgSideNav">
    <h2 class="pgBlockHide">Section Navigation</h2>
';

$navigate_general = $navigate_header.'
    <ul>
      <li><a href="ver.html">Installed products</a></li>
      <li><a href="pgconfig.html">pg_config</a></li>
      <li><a href="pgcontroldata.html">pg_controldata</a></li>
      <li><a href="param.html">General configuration</a></li>
      <li><a href="nondefaultparam.html">Non Default configuration</a></li>';

if ($g_version > '80' && $g_version < '84') {
  $navigate_general .= '
      <li><a href="paramautovac.html">Autovacuum configuration</a></li>';
}

if ($g_superuser && $g_version > '74'
    && (!strcmp($g_settings['log_destination'], 'stderr')
         || !strcmp($g_settings['log_destination'], 'csvlog'))
    && ((array_key_exists('redirect_stderr', $g_settings)
           && !strcmp($g_settings['redirect_stderr'], 'on'))
         || (array_key_exists('logging_collector', $g_settings)
           && !strcmp($g_settings['logging_collector'], 'on'))) ) {

  $navigate_general .= '
      <li class="last-child"><a href="lastlogfile.html">Last log file</a></li>';

}

$navigate_general .= '
    </ul>
  </div>
</div>
';


$navigate_globalobjects = $navigate_header.'
    <ul>
      <li><a href="bases.html">Databases</a></li>';

if ($g_version > '80') {
  $navigate_globalobjects .= '
      <li><a href="graph_dbsize.html">Databases Size Graph</a></li>';
}
if ($g_pgbuffercache) {
  $navigate_globalobjects .= '
      <li><a href="databasesincache.html">Databases in cache</a></li>';
}
if ($g_version > '80') {
  $navigate_globalobjects.= '
      <li><a href="roles.html">Roles</a></li>';
} else {
  $navigate_globalobjects.= '
      <li><a href="users.html">Users</a></li>';
}
$navigate_globalobjects.= '
      <li><a href="user1.html">Users\' Objects</a></li>
      <li><a href="user2.html">Users Space allocated</a></li>';
if ($g_version >= '90') {
  $navigate_globalobjects.= '
      <li><a href="dbrolesettings.html">DB/Role Settings</a></li>';
}
if ($g_version > '74') {
  $navigate_globalobjects.= '
      <li><a href="tablespaces.html">Tablespaces</a></li>';
  if ($g_superuser && $g_version > '80') {
    $navigate_globalobjects .= '
      <li><a href="graph_tblspcsize.html">Tablespaces Size Graph</a></li>';
  }
  $navigate_globalobjects.= '
      <li><a href="tblspc1.html">Tablespaces\' objects</a></li>';
}
$navigate_globalobjects.= '
      <li><a href="largeobjects.html">Large
      Objects</a></li>';
if ($g_version > '80') {
  $navigate_globalobjects.= '
      <li class="last-child"><a href="xlog.html">WAL files</a></li>';
}
$navigate_globalobjects.= '
    </ul>
  </div>
</div>
';

$navigate_dbobjects = $navigate_header.'
    <ul>
      <li><a href="schemas.html">Schemas</a></li>';
if ($g_version >= '90') {
  $navigate_dbobjects.= '
      <li><a href="defaultacl.html">Default ACLs</a></li>';
}
$navigate_dbobjects.= '
      <li><a href="tables.html">Tables</a></li>';

if ($g_version > '80') {
  $navigate_dbobjects .= '
      <li><a href="graph_tablesize.html">Tables Size Graph</a></li>';
}
if ($g_pgbuffercache) {
  $navigate_dbobjects .= '
      <li><a href="tablesincache.html">Tables in cache</a></li>';
}

if ($g_pgstattuple) {
  $navigate_dbobjects .= '
      <li><a href="fragmentedtables.html">Fragmented Tables</a></li>';
}

$navigate_dbobjects .= '
      <li><a href="tableswithoutpkey.html">Tables Without PKEY</a></li>
      <li><a href="tableswith5+indexes.html">Tables With 5+ indexes</a></li>
      <li><a href="clusteredtables.html">Clustered Tables</a></li>
      <li><a href="fkconstraints.html">Tables With FKEY constraints</a></li>
      <li><a href="fkwithoutindexes.html">FKEY without indexes</a></li>
      <li><a href="views.html">Views</a></li>';
if ($g_version > '92') {
  $navigate_dbobjects .= '
      <li><a href="materializedviews.html">Materialized views</a></li>';
}

$navigate_dbobjects .= '
      <li><a href="sequences.html">Sequences</a></li>
      <li><a href="sequences2.html">Sequences - metadata</a></li>
      <li><a href="indexes.html">Indexes</a></li>';

if ($g_version > '80') {
  $navigate_dbobjects .= '
      <li><a href="graph_indexsize.html">Indexes Size Graph</a></li>';
}
if ($g_version > '80') {
  $navigate_dbobjects .= '
      <li><a href="indexesbiggerthantables.html">Indexes Bigger Than Tables</a></li>';
}

if ($g_version > '82') {
  $navigate_dbobjects .= '
      <li><a href="uselessindexes.html">Useless Indexes</a></li>';
}

if ($g_pgstatindex) {
  $navigate_dbobjects .= '
      <li><a href="fragmentedindexes.html">Fragmented Indexes</a></li>';
}

if ($g_version > '80') {
  $navigate_dbobjects.= '
      <li><a href="relationsbloat.html">Relations Bloat</a></li>';
}

$navigate_dbobjects.= '
      <li><a href="functions.html">Functions</a></li>
      <li class="last-child"><a href="languages.html">Languages</a></li>';

if ($g_version >= '91') {
  $navigate_dbobjects.= '
      <li><a href="extensions.html">Extensions</a></li>';
}

$navigate_dbobjects.= '
    </ul>
  </div>
</div>
';

$navigate_activities = $navigate_header.'
    <ul>
      <li><a href="sessionsinfo.html">Sessions</a></li>
      <li><a href="activities.html">Processes</a></li>
      <li><a href="nonidleprocesses.html">Non Idle Processes</a></li>';
if ($g_version >= '91') {
  $navigate_activities .= '
      <li><a href="replication.html">Replication processes</a></li>';
}
if ($g_version >= '82') {
  $navigate_activities .= '
      <li><a href="cursors.html">Cursors</a></li>
      <li><a href="preparedstatements.html">Prepared statements</a></li>
      <li><a href="preparedxacts.html">Prepared transactions</a></li>';
}
$navigate_activities .= '
      <li><a href="locks.html">Locks</a></li>';
if ($g_version > '80') {
  $navigate_activities .= '
      <li><a href="exclusivelocks.html">Exclusive Locks</a></li>';
}
if ($g_version < '90') {
  $navigate_activities .= '
      <li class="last-child"><a href="listeners.html">Listeners</a></li>';
}
$navigate_activities .= '
    </ul>
  </div>
</div>
';

$navigate_stats = $navigate_header.'
    <ul>';
if ($g_version >= '83') {
  $navigate_stats .= '
      <li><a href="bgwriter.html">bgwriter</a></li>';
}
if ((array_key_exists('stats_block_level', $g_settings)
     && !strcmp($g_settings['stats_block_level'], 'on'))
   ||
   (array_key_exists('track_counts', $g_settings)
     && !strcmp($g_settings['track_counts'], 'on'))) {
  $navigate_stats .= '
      <li><a href="cachehitratio.html">Cache hit ratio</a></li>';
}
$navigate_stats .= '
      <li><a href="stat_databases.html">Databases</a></li>';
if ($g_version >= '91') {
  $navigate_stats .= '
      <li><a href="stat_databases_conflicts.html">Databases conflicts</a></li>';
}
$navigate_stats .= '
      <li><a href="stat_tables.html">Tables (Tuples)</a></li>
      <li><a href="statio_tables.html">Tables (IO)</a></li>';
if ($g_version >= '82') {
  $navigate_stats .= '
      <li><a href="lastvacuumtables.html">Last vacuumed Tables</a></li>
      <li><a href="lastanalyzetables.html">Last analyzed Tables</a></li>';
}
if ($g_fsmrelations) {
  $navigate_stats .= '
      <li><a href="fsmrelations.html">FSM Relations</a></li>';
}
if ($g_fsmpages) {
  $navigate_stats .= '
      <li><a href="fsmpages.html">FSM Pages</a></li>';
}
$navigate_stats .= '
      <li><a href="stat_indexes.html">Indexes (Tuples)</a></li>
      <li><a href="statio_indexes.html">Indexes (IO)</a></li>
      <li><a href="statio_sequences.html">Sequences (IO)</a></li>';
if ($g_version >= '84') {
  $navigate_stats .= '
      <li><a href="stat_functions.html">Functions (Runs)</a></li>';
}
$navigate_stats .= '
    </ul>
  </div>
</div>
';

$navigate_tools = $navigate_header.'<h2>Other tools</h2>
<ul>';

if ($g_pgpool) {
  $navigate_tools .= '
      <li class="last-child"><a href="pgpool.html">pgPool</a></li>';
}

$navigate_tools .= '
    </ul>
  </div>
</div>
';

?>
