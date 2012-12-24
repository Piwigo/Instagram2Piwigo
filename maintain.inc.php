<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define(
  'Instagram2Piwigo_default_config', 
  serialize(array(
    'api_key' => null,
    'secret_key' => null,
    ))
  );


function plugin_install() 
{
  global $conf;
  
  conf_update_param('Instagram2Piwigo', Instagram2Piwigo_default_config);
  
  mkdir($conf['data_location'].'instagram_cache/', 0755);
}

function plugin_activate()
{
  global $conf;

  if (empty($conf['Instagram2Piwigo']))
  {
    conf_update_param('Instagram2Piwigo', Instagram2Piwigo_default_config);
  }
  
  if (!file_exists($conf['data_location'].'instagram_cache/'))
  {
    mkdir($conf['data_location'].'instagram_cache/', 0755);
  }
}

function plugin_uninstall() 
{
  global $conf;
  
  pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "Instagram2Piwigo" LIMIT 1;');
  
  rrmdir($conf['data_location'].'instagram_cache/');
}

function rrmdir($dir)
{
  if (!is_dir($dir))
  {
    return false;
  }
  $dir = rtrim($dir, '/');
  $objects = scandir($dir);
  $return = true;
  
  foreach ($objects as $object)
  {
    if ($object !== '.' && $object !== '..')
    {
      $path = $dir.'/'.$object;
      if (filetype($path) == 'dir') 
      {
        $return = $return && rrmdir($path); 
      }
      else 
      {
        $return = $return && @unlink($path);
      }
    }
  }
  
  return $return && @rmdir($dir);
} 

?>