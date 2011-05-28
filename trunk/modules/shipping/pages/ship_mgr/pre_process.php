<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011 PhreeSoft, LLC             |
// | http://www.PhreeSoft.com                                        |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/shipping/pages/ship_mgr/pre_process.php
//
define('DEFAULT_MOD_DIR', DIR_FS_WORKING . 'methods/');
$security_level = validate_user(SECURITY_ID_SHIPPING_MANAGER);
/**************  include page specific files    *********************/
/**************   page specific initialization  *************************/
$date        = $_GET['search_date']       ? gen_db_date($_GET['search_date']) : date('Y-m-d', time());
$search_text = $_GET['search_text'] == TEXT_SEARCH ? ''         : db_input($_GET['search_text']);
$action      = isset($_GET['action'])     ? $_GET['action']     : $_POST['todo'];
$module_id   = isset($_POST['module_id']) ? $_POST['module_id'] : '';
$row_seq     = isset($_POST['rowSeq'])    ? $_POST['rowSeq']    : '';

$file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
$directory_array = array();
// load methods
$installed_modules = load_all_methods('shipping');

/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_MODULES . 'custom/pages/ship_mgr/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }

/***************   Act on the action request   *************************/
switch ($action) {
  case 'track':
    if (!$module_id) break;
	require_once (DIR_FS_WORKING . 'methods/' . $module_id . '/' . $module_id . '.php');
	$tracking = new $module_id;
	$tracking->trackPackages($date, $row_seq);
	break;
  case 'reconcile':
    if (!$module_id) break;
	require_once (DIR_FS_WORKING . 'methods/' . $module_id . '/' . $module_id . '.php');
	$reconcile = new $module_id;
	$reconcile->reconcileInvoice();
	break;
  case 'search':
  case 'search_reset': 
  default:
}

/*****************   prepare to display templates  *************************/
$cal_ship = array(
  'name'      => 'cal',
  'form'      => 'ship_mgr',
  'fieldname' => 'search_date',
  'imagename' => 'btn_date_1',
  'default'   => gen_locale_date($date),
  'params'    => array('align' => 'left', 'onchange' => 'calendarPage();'),
);

$include_header   = true;
$include_footer   = true;
$include_tabs     = true;
$include_calendar = true;
$include_template = 'template_main.php';
define('PAGE_TITLE', BOX_SHIPPING_MANAGER);

?>