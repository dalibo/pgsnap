<?php

function tr($namespace = '') {
  global $odd;

  $odd = !$odd;

  if (strlen($namespace) > 0) {
    if (!strcmp($namespace, 'information_schema')
      || !strcmp($namespace, 'pg_catalog')
      || !strcmp(substr($namespace, 0, 8), 'pg_toast')) {
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

function add_sys_and_user_checkboxes() {
  global $buffer;

  $buffer .= '<label><input id ="showusrobjects" type="checkbox" checked>Show User Objects</label>
<label><input id ="showsysobjects" type="checkbox" checked>Show System Objects</label>';
}

?>
