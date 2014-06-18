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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Table Size Graph</h1>';

$query = "SELECT nspname, relname,
  pg_relation_size(pg_class.oid)/1024/1024 AS size
FROM pg_class, pg_namespace
WHERE relkind = 'r'
  AND relnamespace = pg_namespace.oid
  AND pg_relation_size(pg_class.oid)/1024/1024 > 1";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY 3 DESC";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  $data = '';
  $ticks = '';
  $tablenames = '';
  $tablecomments = '';
  $i = 1;
  while ($row = pg_fetch_array($rows)) {
    if (strlen($data)>0)
      $data .= ",";
    if (strlen($ticks)>0)
      $ticks .= ",";
    if (strlen($tablenames)>0)
      $tablenames .= ",";
    if (strlen($tablecomments)>0)
      $tablecomments .= ",";
    $data .= "[".$i.",".$row['size']."]";
    $ticks .= "[".$i.",'".$row['relname']."']";
    $tablenames .= "'".$row['nspname'].".".$row['relname']."'";
    $tablecomments .= "'".$comments['relations'][$row['nspname']][$row['relname']]."'";
    $i++;
  }

  $buffer .= '<div id="graphcontainer" width="600" height="400"></div>
<script type="text/javascript">
  (function () {
    var data, graph;

    data = ['.$data.'];
    graph = Flotr.draw(document.getElementById(\'graphcontainer\'), [ data ],
    {
      HtmlText: false,
      bars: {
        show: true,
        horizontal: false,
        shadowSize: 0,
        barWidth: 0.5
      },
      xaxis: {
        ticks: ['.$ticks.'],
        labelsAngle: 45
      },
      yaxis: {
        min: 0,
        autoscaleMargin: 1
      },
      mouse: {
        track: true,
        relative: true,
        trackFormatter: function(point) {
          var dbnames=['.$tablenames.'];
          var dbcomments=['.$tablecomments.'];
          return dbnames[Math.floor(point.x) - 1]
                 + "<br/>"
                 + point.y.toString() + "MB"
                 + "<br/>"
                 + dbcomments[Math.floor(point.x) - 1];
        }
      },
    });
    })();
</script>';
} else {
  $buffer .= '<div class="warning">No table of more than 1 MB!</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/graph_tablesize.html';
include 'lib/fileoperations.php';

?>
