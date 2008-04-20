<?php

function tr() {
  global $odd;

  $odd = !$odd;
  if ($odd) {
    $tr = '<tr class="odd">';
  } else {
    $tr = '<tr>';
  }
  return $tr;
}

?>