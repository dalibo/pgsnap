<?php

/*
 * Copyright (c) 2008-2015 Guillaume Lelarge <guillaume@lelarge.info>
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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Databases Size Graph</h1>';

$query = 'SELECT datname,
  pg_database_size(datname)/1024/1024 AS size
FROM pg_database
WHERE pg_database_size(datname)/1024/1024 > 1
ORDER BY 2 DESC';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  $data = '';
  $ticks = '';
  $dbnames = '';
  $dbcomments = '';
  $i = 1;
  while ($row = pg_fetch_array($rows)) {
    if (strlen($data)>0)
      $data .= ",";
    if (strlen($ticks)>0)
      $ticks .= ",";
    if (strlen($dbnames)>0)
      $dbnames .= ",";
    if (strlen($dbcomments)>0)
      $dbcomments .= ",";
    $data .= "[".$i.",".$row['size']."]";
    $ticks .= "[".$i.",'".$row['datname']."']";
    $dbnames .= "'".$row['datname']."'";
    $dbcomments .= "'".$comments['databases'][$row['datname']]."'";
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
          var dbnames=['.$dbnames.'];
          var dbcomments=['.$dbcomments.'];
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
  $buffer .= '<div class="warning">No database of more than 1 MB!</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/graph_dbsize.html';
include 'lib/fileoperations.php';

?>
