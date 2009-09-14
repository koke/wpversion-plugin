<?php
/*
Plugin Name: wpversion exchange point
Plugin URI: http://wpversion.com/plugin
Description: Provides information about current version to wpversion.com
Version: 0.1
Author: Jorge Bernal
Author URI: http://www.jorgebernal.info/
*/

/*  Copyright 2009  Jorge Bernal  (email : koke@amedias.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$wpv_version = 0.1;

function wpv_permalinks($rules) {
  global $wp_rewrite;

  $newrules['wpv/?'] = 'index.php?showwpv=1';
  $newrules = array_merge($newrules,$rules);
  return $newrules;
} 


function wpv_query_vars ( $vars ) {
  $vars[] = "showwpv";
  return $vars;
}

function wpv_genkey() {
  return md5(microtime(true).mt_rand(10000,90000));
}

/*
function wpv_activate()
{
  if (!get_option("wpv_key")) {
    $genkey = wpv_genkey();
    add_option("wpv_key", $genkey, '', 'yes');
  }
}
*/

function wpv_menu() {
  add_options_page('wpversion', 'wpversion', 8, 'wpversion', 'wpv_options');
}

function wpv_options() {
  $auth_key = get_option("wpv_key");
  if (!$auth_key or $auth_key == "") {
    $auth_key = wpv_genkey();
    add_option("wpv_key", $auth_key, '', 'yes');
  }
  
  echo '<div class="wrap">';
  echo "<p>Your auth key: <span>$auth_key</span></p>";
  echo '</div>';
}

function wpv_error($err)
{
  echo "Error: $err";
}

function wpv_ok($msg)
{
  echo "OK: $msg";
}

function wpv_action() {
  $authkey = get_option('wpv_key');
  $action = $_GET["action"];
  
  if ($action == "check") {
    global $wpv_version;
    echo $wpv_version;
  } else {
    $key = $_GET["key"];
    if (!$key) {
      wpv_error("You need to provide an auth key");
    } elseif ($key != $authkey) {
      wpv_error("Wrong auth key");
    } else {
      switch ($action) {
        case 'version':
          global $wp_version;
          wpv_ok($wp_version);
          break;
        
        default:
          wpv_error("Unknown action");
          break;
      }
    }
  }
}

function wpv_show_rewrites($content) {
  global $wp_rewrite;

  if (get_query_var("showwpv")) {    
    wpv_action();
    exit();
  }
}

add_filter('query_vars', 'wpv_query_vars');
add_filter('rewrite_rules_array', 'wpv_permalinks'); 
add_action('template_redirect', 'wpv_show_rewrites');
add_action('admin_menu', 'wpv_menu');

// register_activation_hook( __FILE__, 'wpv_activate' );

?>