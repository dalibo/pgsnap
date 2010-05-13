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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Tablespaces Size Graph</h1>';

$query = 'SELECT spcname,
  pg_tablespace_size(spcname)/1024/1024 AS size
FROM pg_tablespace
WHERE pg_tablespace_size(spcname)/1024/1024 > 1
ORDER BY 2 DESC';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

if (pg_num_rows($rows) > 0) {
  include_once( 'external/open-flash-chart.php' );

  $bar = new bar_outline( 50, 6, '#99FF00', '#7030A0' );

  $data = array();
  $labels = array();

  $max = 0;
  while ($row = pg_fetch_array($rows)) {
    if ($max < $row['size']) {
      $max = $row['size'];
    }
    $bar->add_data_tip($row['size'], $comments['tablespaces'][$row['spcname']]);
    $labels[] = $row['spcname'];
  }

  $g = new graph();
  $g->title( 'Tablespaces size in MB', '{font-size: 18px; color: #A0A0A0;}' );
  $g->set_tool_tip( '#x_label#<br>#tip#<br>#val# MB' );
  $g->set_x_labels( $labels );
  $g->data_sets[] = $bar;
  $g->set_x_label_style( 10, '#A0A0A0', 0, 1 );
  $g->set_y_label_style( 10, '#A0A0A0' );
  $g->x_axis_colour( '#A0A0A0', '#FFFFFF' );
  $g->set_x_legend( 'Databases\' names', 12, '#A0A0A0' );
  $g->y_axis_colour( '#A0A0A0', '#FFFFFF' );
  $g->set_y_min( 0 );
  $g->set_y_max( $max );
  $g->y_label_steps( 10 );
  $g->set_width( 500 );
  $g->set_height( 300 );
  $g->set_output_type('js');
  $buffer .= $g->render();
} else {
  $buffer .= '<div class="warning">No tablespace of more than 1 MB!</div>';
}

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/graph_tblspcsize.html';
include 'lib/fileoperations.php';

?>
