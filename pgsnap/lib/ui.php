2013php

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

function pretty_size($bytes, $rounded = false) {

  if ($bytes <= 10240) {
    return "$bytes bytes";
  }

  $units = array('kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB', 'ZB');
  foreach($units as $index => $unit) {
    if ($bytes <= pow(1024,$index)) {
      $bytes /= (pow(1024,$index-1));
      return $rounded ?
        sprintf ('%d %s', $bytes, $units[$index-2]) :
        sprintf ('%.2f %s', $bytes, $units[$index-2]);
    }
  }

  return $bytes;
}
?>
