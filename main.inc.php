<?php 
/*
Plugin Name: Instagram2Piwigo
Version: auto
Description: Import pictures from your instagram account
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'instagram2piwigo')
{
  add_event_handler('init', 'instagram_error');
  function instagram_error()
  {
    global $page;
    $page['errors'][] = 'Instagram2Piwigo folder name is incorrect, uninstall the plugin and rename it to "instagram2piwigo"';
  }
  return;
}

global $conf;

define('INSTAG_PATH',     PHPWG_PLUGINS_PATH . 'instagram2piwigo/');
define('INSTAG_ADMIN',    get_root_url() . 'admin.php?page=plugin-instagram2piwigo');
define('INSTAG_FS_CACHE', $conf['data_location'].'instagram_cache/');

include_once(INSTAG_PATH . 'include/ws_functions.inc.php');

$conf['Instagram2Piwigo'] = safe_unserialize($conf['Instagram2Piwigo']);


add_event_handler('ws_add_methods', 'instagram_add_ws_methodV2');

if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'instagram_admin_menu');

  add_event_handler('get_batch_manager_prefilters', 'instagram_add_batch_manager_prefilters');
  add_event_handler('perform_batch_manager_prefilters', 'instagram_perform_batch_manager_prefilters', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
}


function instagram_admin_menu($menu) 
{
  $menu[] = array(
    'NAME' => 'Instagram2Piwigo',
    'URL' => INSTAG_ADMIN,
    );
  return $menu;
}

function instagram_add_batch_manager_prefilters($prefilters)
{
  $prefilters[] = array(
    'ID' => 'instagram',
    'NAME' => l10n('Imported from Instagram'),
    );
  return $prefilters;
}

function instagram_perform_batch_manager_prefilters($filter_sets, $prefilter)
{
  if ($prefilter == 'instagram')
  {
    $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE file LIKE "instagram-%"
;';
    $filter_sets[] = array_from_query($query, 'id');
  }
  
  return $filter_sets;
}