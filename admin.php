<?php
if (!defined('INSTAG_PATH')) die('Hacking attempt!');

global $template, $page, $conf;

$conf['Instagram2Piwigo'] = unserialize($conf['Instagram2Piwigo']);
load_language('plugin.lang', INSTAG_PATH);

if (!file_exists(INSTAG_FS_CACHE))
{
  mkdir(INSTAG_FS_CACHE, 0755);
}

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'import';
  
$tabsheet = new tabsheet();
$tabsheet->add('import', l10n('Import'), INSTAG_ADMIN . '-import');
$tabsheet->add('config', l10n('Configuration'), INSTAG_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// include page
include(INSTAG_PATH . 'admin/' . $page['tab'] . '.php');

// template
$template->assign(array(
  'INSTAG_PATH'=> INSTAG_PATH,
  'INSTAG_ABS_PATH'=> dirname(__FILE__).'/',
  'INSTAG_ADMIN' => INSTAG_ADMIN,
  ));
$template->assign_var_from_handle('ADMIN_CONTENT', 'Instagram2Piwigo');

?>