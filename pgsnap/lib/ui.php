<?php

function tr($namespace = '') {
  global $odd;

  $odd = !$odd;

  if (strlen($namespace) > 0) {
    if (!strcmp($namespace, 'information_schema')
      or !strcmp($namespace, 'pg_catalog')
      or !strcmp(substr($namespace, 0, 8), 'pg_toast')) {
      $class = 'sys';
    } else {
      $class = 'usr';
    }
  }
  else $class = '';
  if ($odd) {
    if (strlen($class) > 0) {
      $class .= '_';
    }
    $class .= 'odd';
  }

  $tr = '<tr class="'.$class.'">';

  return $tr;
}

?>
