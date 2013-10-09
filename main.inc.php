<?php 
/*
Plugin Name: Instagram2Piwigo
Version: auto
Description: Import pictures from your instagram account
Plugin URI: auto
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
  add_event_handler('get_batch_manager_prefilters', 'instagram_add_batch_manager_prefilters');
  add_event_handler('perform_batch_manager_prefilters', 'instagram_perform_batch_manager_prefilters', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
  add_event_handler('loc_begin_admin_page', 'instagram_prefilter_from_url');

  function instagram_admin_menu($menu) 
  {
    array_push($menu, array(
      'NAME' => 'Instagram2Piwigo',
      'URL' => INSTAG_ADMIN,
    ));
    return $menu;
  }
  
  function instagram_add_batch_manager_prefilters($prefilters)
  {
    array_push($prefilters, array(
      'ID' => 'instagram',
      'NAME' => l10n('Imported from Instagram'),
    ));
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
  
  function instagram_prefilter_from_url()
  {
    global $page;
    if ($page['page'] == 'batch_manager' && @$_GET['prefilter'] == 'instagram')
    {
      $_SESSION['bulk_manager_filter'] = array('prefilter' => 'instagram');
      unset($_GET['prefilter']);
    }
  }
}


include_once(INSTAG_PATH . 'include/ws_functions.inc.php');

add_event_handler('ws_add_methods', 'instagram_add_ws_method');

?>