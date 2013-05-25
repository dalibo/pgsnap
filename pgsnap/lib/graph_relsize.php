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

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Relations Size Graph</h1>';

$query = "SELECT
  CASE WHEN relkind='r' THEN 'table'
       WHEN relkind='i' THEN 'index'
       WHEN relkind='S' THEN 'sequence'
       WHEN relkind='m' THEN 'materializedview'
       WHEN relkind='t' THEN 'TOASTtable'
       ELSE '<unkown>' END AS kind,
  sum(pg_relation_size(pg_class.oid)) AS size
FROM pg_class, pg_namespace
WHERE relkind IN ('r', 'i', 't', 'm')
  AND relnamespace = pg_namespace.oid
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'
GROUP BY 1
ORDER BY 1 ASC";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  $declarations = '';
  $statements = '';
  $i = 1;
  while ($row = pg_fetch_array($rows)) {
    if (strlen($declarations)>0)
      $declarations .= ",";
    if (strlen($statements)>0)
      $statements .= ",";
    $declarations .= "d".$i." = [[0, ".$row['size']."]]";
    $statements .= "{data: d".$i.",label: '".$row['kind']."'}";
    $i++;
  }

  $buffer .= '<div id="graphcontainer" width="600" height="400"></div>
<script type="text/javascript">
  (function () {
    var '.$declarations.',
        graph;

    graph = Flotr.draw(document.getElementById(\'graphcontainer\'),
      ['.$statements.'],
    {
        HtmlText: false,
        grid: {
            verticalLines: false,
            horizontalLines: false
        },
        xaxis: {
            showLabels: false
        },
        yaxis: {
            showLabels: false
        },
        pie: {
            show: true,
          explode: 0
        },
        legend: {
            position: \'se\',
            backgroundColor: \'#D2E8FF\'
        }
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

$filename = $outputdir.'/graph_relsize.html';
include 'lib/fileoperations.php';

?>
