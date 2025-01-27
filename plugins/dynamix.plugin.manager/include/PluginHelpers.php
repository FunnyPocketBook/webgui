<?PHP
/* Copyright 2005-2022, Lime Technology
 * Copyright 2012-2022, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
?>
<?
// Invoke the plugin command with indicated method
function plugin($method, $arg = '') {
  global $docroot;
  exec("$docroot/plugins/dynamix.plugin.manager/scripts/plugin ".escapeshellarg($method)." ".escapeshellarg($arg), $output, $retval);
  return $retval==0 ? implode("\n", $output) : false;
}

// Invoke the language command with indicated method
function language($method, $arg = '') {
  global $docroot;
  exec("$docroot/plugins/dynamix.plugin.manager/scripts/language ".escapeshellarg($method)." ".escapeshellarg($arg), $output, $retval);
  return $retval==0 ? implode("\n", $output) : false;
}

function check_plugin($arg, &$ncsi) {
// Get network connection status indicator (NCSI)
  if ($ncsi===null) $ncsi = exec("wget --spider --no-check-certificate -nv -T10 -t1 https://www.msftncsi.com/ncsi.txt 2>&1|grep -o 'OK'");
  return $ncsi ? plugin('check',$arg) : false;
}

function make_link($method, $arg, $extra='') {
  $plg = basename($arg,'.plg').':'.$method;
  $id = str_replace(['.',' ','_'],'',$plg);
  $check = $method=='remove' ? "<input type='checkbox' onClick='document.getElementById(\"$id\").disabled=!this.checked'>" : "";
  $disabled = $check ? ' disabled' : '';
  if ($method == 'delete') {
    $cmd = "/plugins/dynamix.plugin.manager/scripts/plugin_rm&arg1=$arg";
    $exec = $plg = "";
  } else {
    $cmd = "/plugins/dynamix.plugin.manager/scripts/plugin&arg1=$method&arg2=$arg".($extra?"&arg3=$extra":"");
    $exec = "loadlist";
  }
  return "$check<input type='button' id='$id' value='"._(ucfirst($method))."' onclick='openBox(\"$cmd\",\""._(ucwords($method)." Plugin")."\",600,900,true,\"$exec\",\"$plg\");'$disabled>";
}

// trying our best to find an icon
function icon($name) {
// this should be the default location and name
  $icon = "plugins/$name/images/$name.png";
  if (file_exists($icon)) return $icon;
// try alternatives if default is not present
  $icon = "plugins/$name/$name.png";
  if (file_exists($icon)) return $icon;
  $image = @preg_split('/[\._- ]/',$name)[0];
  $icon = "plugins/$name/images/$image.png";
  if (file_exists($icon)) return $icon;
  $icon = "plugins/$name/$image.png";
  if (file_exists($icon)) return $icon;
// last resort - default plugin icon
  return "webGui/images/plg.png";
}
function mk_options($select,$value) {
  return "<option value='$value'".($select==$value?" selected":"").">"._(ucfirst($value))."</option>";
}
?>
