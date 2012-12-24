<?php 
/*
Plugin Name: Instagram2Piwigo
Version: auto
Description: Import pictures from your Flickr account
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=612
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $conf;

define('INSTAG_PATH', PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
define('INSTAG_ADMIN', get_root_url() . 'admin.php?page=plugin-' . basename(dirname(__FILE__)));
define('INSTAG_FS_CACHE', $conf['data_location'].'instagram_cache/');


if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'instagram_admin_menu');

  function instagram_admin_menu($menu) 
  {
    array_push($menu, array(
      'NAME' => 'Instagram2Piwigo',
      'URL' => INSTAG_ADMIN,
    ));
    return $menu;
  }
}


include_once(INSTAG_PATH . 'include/ws_functions.inc.php');

add_event_handler('ws_add_methods', 'instagram_add_ws_method');

?>