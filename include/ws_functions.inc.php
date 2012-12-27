<?php
if (!defined('INSTAG_PATH')) die('Hacking attempt!');

function instagram_add_ws_method($arr)
{
  $service = &$arr[0];
  
  $service->addMethod(
    'pwg.images.addInstagram',
    'ws_images_addInstagram',
    array(
      'id' => array(),
      'category' => array(),
      'fills' => array('default' =>null),
      ),
    'Used by Instagram2Piwigo'
    );
}

function ws_images_addInstagram($param, &$service)
{
  if (!is_admin())
  {
    return new PwgError(403, 'Forbidden');
  }
  
  global $conf;
  $conf['Instagram2Piwigo'] = unserialize($conf['Instagram2Piwigo']);
  
  if ( empty($conf['Instagram2Piwigo']['api_key']) or empty($conf['Instagram2Piwigo']['secret_key']) )
  {
    return new PwgError(null, l10n('Please fill your API keys on the configuration tab'));
  }
  
  include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');
  include_once(PHPWG_ROOT_PATH . 'admin/include/functions_upload.inc.php');
  include_once(INSTAG_PATH . 'include/functions.inc.php');
  
  if (!function_exists('curl_init'))
  {
    return new PwgError(null, l10n('No download method available'));
  }
  
  // init instagram API
  if (empty($_SESSION['instagram_access_token']))
  {
    return new PwgError(403, l10n('API not authenticated'));
  }
  
  require_once(INSTAG_PATH . 'include/Instagram/Instagram.php');
  
  $instagram = new Instagram($_SESSION['instagram_access_token']);
  $instagram->enableCache(INSTAG_FS_CACHE);
  
  $current_user = $instagram->getCurrentUser();
  $username = $current_user->getData()->username;
  
  // photos infos
  $photo_f = $instagram->getMedia($param['id']);
  $photo = array(
    'id' => $photo_f->id,
    'title' => $photo_f->getCaption(),
    'url' => $photo_f->getStandardRes()->url,
    'date' => $photo_f->getCreatedTime('Y-d-m H:i:s'),
    'username' => $photo_f->getUser()->getFullName(true),
    'tags' => $photo_f->getTags()->toArray(),
    );
  $photo = array_merge($param, $photo);
  
  if (!empty($photo['title']))
  {
    $photo['title'] = $photo['title']->getText();
  }
  else
  {
    $photo['title'] = $photo['id'];
  }
  
  $photo['path'] = INSTAG_FS_CACHE . 'instagram-'.$username.'-'.$photo['id'].'.'.get_extension($photo['url']);
  
  // copy file
  if (download_remote_file($photo['url'], $photo['path']) == false)
  {
    return new PwgError(null, l10n('Can\'t download file'));
  }
  
  // add photo
  $photo['image_id'] = add_uploaded_file($photo['path'], basename($photo['path']), array($photo['category']));
  
  // do some updates
  if (!empty($photo['fills']))
  {
    $photo['fills'] = rtrim($photo['fills'], ',');
    $photo['fills'] = explode(',', $photo['fills']);
  
    $updates = array();
    if (in_array('fill_name', $photo['fills']))   $updates['name'] = $photo['title'];
    if (in_array('fill_taken', $photo['fills']))  $updates['date_creation'] = $photo['date'];
    if (in_array('fill_author', $photo['fills'])) $updates['author'] = $photo['username'];
    
    if (count($updates))
    {
      single_update(
        IMAGES_TABLE,
        $updates,
        array('id' => $photo['image_id'])
        );
    }
    
    if ( !empty($photo['tags']) and in_array('fill_tags', $photo['fills']) )
    {
      $raw_tags = implode(',', $photo['tags']);
      set_tags(get_tag_ids($raw_tags), $photo['image_id']);
    }
  }
  
  return sprintf(l10n('Photo "%s" imported'), $photo['title']);
}

?>