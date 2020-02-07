<?php
defined('INSTAG_PATH') or die('Hacking attempt!');

set_time_limit(600);

include_once(INSTAG_PATH . 'include/functions.inc.php');

// check API parameters and connect to instagram
if (empty($conf['Instagram2Piwigo']['api_key']) or empty($conf['Instagram2Piwigo']['secret_key']))
{
  $page['warnings'][] = l10n('Please fill your API keys on the configuration tab');
  $_GET['action'] = 'error';
}
else if (!function_exists('curl_init'))
{
  $page['errors'][] = l10n('No download method available').' (cURL)';
  $_GET['action'] = 'error';
}
else
{
  // init instagram API
  if (empty($_SESSION['instagram_access_token']))
  {
    require_once(INSTAG_PATH . 'include/Instagram/Auth.php');
    
    $auth_config = array(
      'client_id'     => $conf['Instagram2Piwigo']['api_key'],
      'client_secret' => $conf['Instagram2Piwigo']['secret_key'],
      'redirect_uri'  => get_absolute_root_url() . INSTAG_ADMIN . '-import',
      );
    
    $auth = new Instagram_Auth($auth_config);
  
    // must authenticate
    if (@$_GET['action'] != 'login')
    {
      $_GET['action'] = 'init_login';
    }
    
    // generate token after authentication
    if (!empty($_GET['code']))
    {
      $_SESSION['instagram_access_token'] = $auth->getAccessToken($_GET['code']);
      $_GET['action'] = 'logued';
    }
  }
  else
  {
    require_once(INSTAG_PATH . 'include/Instagram/Instagram.php');
    $instagram = new Instagram($_SESSION['instagram_access_token']);
    $instagram->enableCache(INSTAG_FS_CACHE);
    
    $current_user = $instagram->getCurrentUser();
    $username = $current_user->getData()->username;
  }
}


if (!isset($_GET['action']))
{
  $_GET['action'] = 'main';
}


switch ($_GET['action'])
{
  // button to login page
  case 'init_login':
  {
    $template->assign('instagram_login', INSTAG_ADMIN . '-import&amp;action=login');
    break;
  }
  
  // call instagram login procedure
  case 'login':
  {
    $auth->authorize();
    break;
  }
  
  // message after login
  case 'logued':
  {
    $_SESSION['page_infos'][] = l10n('Successfully logged in to you Instagram account');
    redirect(INSTAG_ADMIN . '-import');
    break;
  }
  
  // logout
  case 'logout':
  {
    unset($_SESSION['instagram_access_token']);
    $_SESSION['page_infos'][] = l10n('Logged out');
    redirect(INSTAG_ADMIN . '-import');
    break;
  }
  
  // main menu
  case 'main':
  {
    $template->assign(array(
      'username' => $username,
      'profile_url' => 'http://instagram.com/'.$username,
      'logout_url' => INSTAG_ADMIN . '-import&amp;action=logout',
      'list_photos_url' => INSTAG_ADMIN . '-import&amp;action=list_photos',
      ));
    break;
  }
  
  // list photos
  case 'list_photos':
  {
    $self_url = INSTAG_ADMIN . '-import&amp;action=list_photos';
    $instagram_prefix = 'instagram-'.$username.'-';
    
    // pagination
    if (isset($_GET['start']))   $page['start'] = intval($_GET['start']);
    else                         $page['start'] = 0;
    if (isset($_GET['display'])) $page['display'] = $_GET['display']=='all' ? 500 : intval($_GET['display']);
    else                         $page['display'] = 20;
    
    $all_photos = $current_user->getAllMedia();
    
    // get existing photos
    $query = '
SELECT id, file
  FROM '.IMAGES_TABLE.'
  WHERE file LIKE "'.$instagram_prefix.'%"
;';
    
    $existing_photos = simple_hash_from_query($query, 'id', 'file');
	$existing_photos = array_map(function($p) use ($instagram_prefix){return preg_replace('#^'.$instagram_prefix.'([0-9_]+)\.([a-z]{3,4})$#i', "$1", $p);}, $existing_photos);
    
    // remove existing photos
    $duplicates = 0;
    foreach ($all_photos as $i => $photo)
    {
      if (in_array($photo->id, $existing_photos))
      {
        $all_photos->unsetItem($i);
        $duplicates++;
      }
    }
    
    if ($duplicates>0)
    {
      $page['infos'][] = '<a href="admin.php?page=batch_manager&amp;filter=prefilter-instagram">'
          .l10n_dec(
            'One picture is not displayed because already existing in the database.',
            '%d pictures are not displayed because already existing in the database.',
            $duplicates)
        .'</a>';
    }
    
    // displayed photos
    $page_photos = $all_photos->getSlice($page['start'], $page['display']);
	$all_elements = array_map(function($p){return  '"'.$p->id.'"';}, $all_photos->getData());
    
    $tpl_vars = array();
    foreach ($page_photos as $photo)
    {
      $tpl_vars[] = array(
        'id' => $photo->id,
        'title' => $photo->getCaption(),
        'thumb' => $photo->getThumbnail()->url,
        'src' => $photo->getLowRes()->url,
        'url' => $photo->getLink(),
        );
    }
    
    $template->assign(array(
      'nb_thumbs_set' => count($all_photos),
      'nb_thumbs_page' => count($page_photos),
      'thumbnails' => $tpl_vars,
      'all_elements' => $all_elements,
      'F_ACTION' => INSTAG_ADMIN.'-import&amp;action=import_set',
      'U_DISPLAY' => $self_url,
      ));
      
    // get piwigo categories
    $query = '
SELECT id, name, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
;';
    display_select_cat_wrapper($query, array(), 'category_parent_options');
    
    // get navbar
    $nav_bar = create_navigation_bar(
      $self_url,
      count($all_elements),
      $page['start'],
      $page['display']
      );
    $template->assign('navbar', $nav_bar);
    break;
  }
  
  // success message after import
  case 'import_set':
  {
    if (isset($_POST['done']))
    {
      $_SESSION['page_infos'][] = l10n('%d pictures imported', $_POST['done']);
    }
    redirect(INSTAG_ADMIN . '-import');
  }
}


$template->assign('ACTION', $_GET['action']);

$template->set_filename('Instagram2Piwigo', realpath(INSTAG_PATH . 'admin/template/import.tpl'));
