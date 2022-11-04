<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class instagram2piwigo_maintain extends PluginMaintain
{
  private $default_conf = array(
    'api_key' => null,
    'secret_key' => null,
	'begin_sync_date' => null,
    );

  function install($plugin_version, &$errors=array())
  {
    global $conf;

    if (empty($conf['Instagram2Piwigo']))
    {
      conf_update_param('Instagram2Piwigo', $this->default_conf, true);
    }

    mkgetdir(PHPWG_ROOT_PATH . $conf['data_location'] . 'instagram_cache/', MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR);
  }

  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    global $conf;

    conf_delete_param('Instagram2Piwigo');

    self::rrmdir(PHPWG_ROOT_PATH . $conf['data_location'] . 'instagram_cache/');
  }

  static function rrmdir($dir)
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
          $return = $return && self::rrmdir($path);
        }
        else
        {
          $return = $return && @unlink($path);
        }
      }
    }

    return $return && @rmdir($dir);
  }
}
