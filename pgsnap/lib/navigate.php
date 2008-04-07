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

$buffer = '<h1>PGSnap '.$VERSION.'</h1>

<p><a href="links.html" target="content">Home</a></p>

<div>
<h2>General</h2>
<ul>
<li><a href="ver.html" target="content">Installed products</a></li>';
if ($g_version > '74') {
  $buffer .= '
<li><a href="pgconfig.html" target="content">pg_config</a></li>';
}
$buffer .= '
<li><a href="pgcontroldata.html" target="content">pg_controldata</a></li>
<li><a href="param.html" target="content">General configuration</a>';
if ($g_version > '74') {
  $buffer .= '
<li><a href="paramautovac.html" target="content">Autovacuum configuration</a>';
}
$buffer .= '
</div>

<div>
<h2>Global Objects</h2>
<ul>
<li><a href="bases.html" target="content">Databases</a></li>';

if ($g_pgbuffercache) {
  $buffer .= '
<li><a href="databasesincache.html" target="content">Databases in cache</a></li>';
}
$buffer.= '
<li><a href="roles.html" target="content">Roles</a></li>
<li><a href="user1.html" target="content">Users\' Objects</a></li>
<li><a href="user2.html" target="content">Users Space allocated</a></li>
<li><a href="tablespaces.html" target="content">Tablespaces</a></li>
<li><a href="tblspc1.html" target="content">Tablespaces\' objects</a></li>
<li>Large Objects</li>
</ul>
</div>

<div>
<h2>Database Objects</h2>
<ul>
<li><a href="schemas.html" target="content">Schemas</a></li>
<li><a href="tables.html" target="content">Tables</a></li>';

if ($g_pgbuffercache) {
  $buffer .= '
<li><a href="tablesincache.html" target="content">Tables in cache</a></li>';
}

$buffer.= '
<li><a href="tableswithoutpkey.html" target="content">Tables Without PKEY</a></li>
<li><a href="tableswith5+indexes.html" target="content">Tables With 5+ indexes</a></li>
<li><a href="fkconstraints.html" target="content">Tables With FKEY constraints</a></li>
<li><a href="views.html" target="content">Views</a></li>
<li><a href="sequences.html" target="content">Sequences</a></li>
<li><a href="indexes.html" target="content">Indexes</a></li>
<li>Functions</li>
<li><a href="languages.html" target="content">Languages</a></li>
</ul>
</div>

<div>
<h2>Activities</h2>
<ul>
<li><a href="sessionsinfo.html" target="content">Sessions</a></li>
<li><a href="activities.html" target="content">Process</a></li>
<li><a href="locks.html" target="content">Locks</a></li>';
if ($g_version >= '82') {
  $buffer .= '
<li><a href="cursors.html" target="content">Cursors</a></li>
<li><a href="preparedstatements.html" target="content">Prepared statements</a></li>
<li><a href="preparedxacts.html" target="content">Prepared transactions</a></li>';
}
$buffer .= '
</ul>
</div>

<div>
<h2>Statistics</h2>
<ul>';
if ($g_version == '83') {
  $buffer .= '
<li><a href="bgwriter.html" target="content">bgwriter</a></li>';
}
$buffer .= '
<li><a href="stat_databases.html" target="content">Databases</a></li>
<li><a href="stat_tables.html" target="content">Tables</a></li>
<li><a href="lastvacuumtables.html" target="content">Last vacuumed Tables</a></li>
<li><a href="lastanalyzetables.html" target="content">Last analyzed Tables</a></li>
<li><a href="stat_indexes.html" target="content">Indexes</a></li>
</ul>
</div>

<div>
<h2>Other tools</h2>
<ul>
<li><a href="pgpool.html" target="content">pgPool</a></li>
<li>pgPool-II</li>
<li>pgBouncer</li>
<li>Slony</li>
</ul>
</div>';

$filename = $outputdir.'/navigate.html';
include 'lib/fileoperations.php';

?>
