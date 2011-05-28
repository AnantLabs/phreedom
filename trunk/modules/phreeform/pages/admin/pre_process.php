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
//  Path: /modules/phreeform/pages/admin/pre_process.php
//
$security_level = validate_user(SECURITY_ID_CONFIGURATION);
/**************  include page specific files    *********************/
gen_pull_language($module, 'admin');
gen_pull_language('phreedom', 'admin');
require_once(DIR_FS_WORKING . 'defaults.php');
require_once(DIR_FS_WORKING . 'functions/phreeform.php');
require_once(DIR_FS_WORKING . 'classes/install.php');
/**************   page specific initialization  *************************/
$error   = false; 
$action  = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
$install = new phreeform_admin();
/***************   Act on the action request   *************************/
switch ($action) {
  case 'save': 
	if ($security_level < 3) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL')); 
	}
	// save general tab
	foreach ($install->keys as $key => $default) {
	  $field = strtolower($key);
      if (isset($_POST[$field])) write_configure($key, $_POST[$field]);
    }
	$messageStack->add(GENERAL_CONFIG_SAVED, 'success');
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
    break;
/*** BOF - Added by PhreeSoft to convert PhreeBooks reports to phreeform format *************/
  // This script transfers stored reports from the reportwriter database used in PhreeBooks to phreeform
  case 'convert':
	require_once(DIR_FS_MODULES . 'phreeform/functions/reportwriter.php');
	$result = $db->Execute("select * from " . TABLE_REPORTS);
	$count = 0;
	while (!$result->EOF) {
	  $skip_report = false;
	  $report = PrepReport($result->fields['id']);
	  if (!$params = import_text_params($report)) {
	    $messageStack->add(sprintf(PB_CONVERT_ERROR, $result->fields['description']), 'error');
		$skip_report = true;
	  }
	  // fix some fields
	  $params->standard_report = $result->fields['standard_report'] ? 's' : 'c';
	  // error check
	  $duplicate = $db->Execute("select id from " . TABLE_PHREEFORM . " 
	    where doc_title = '" . addslashes($params->title) . "' and doc_type <> '0'");
	  if ($duplicate->RecordCount() > 0) { // the report name already exists, error 
	    $messageStack->add(sprintf(PHREEFORM_REPDUP, $params->title), 'error');
	    $skip_report = true;
	  }
	  if (!$skip_report) {
	    if (!$success = save_report($params)) {
	      $messageStack->add(sprintf(PB_CONVERT_SAVE_ERROR, $params->title), 'error');
		}
		$count++;
	  }
	  $result->MoveNext();
	}
	// Copy the PhreeBooks images
	$dir_source = DIR_FS_MY_FILES . $_SESSION['company'] . '/images';
	$dir_dest   = PF_DIR_MY_REPORTS . 'images';
	$d = dir($dir_source);
	while (FALSE !== ($filename = $d->read())) {
	  if ($filename == '.' || $entry == '..') continue;
	  @copy($dir_source . '/' . $filename, $dir_dest . '/' . $filename);
	}
	$d->close();
	if ($count) $messageStack->add(sprintf(PB_CONVERT_SUCCESS, $count), 'success');
    break;
/*** EOF - Added by PhreeSoft to convert PhreeBooks reports to phreeform format *************/
  default:
}

/*****************   prepare to display templates  *************************/
$pdf_choices = array(
  array('id' => 'TCPDF', 'text' => 'TCPDF'),
  array('id' => 'FPDF',  'text' => 'FPDF'),
);

$include_header   = true;
$include_footer   = true;
$include_tabs     = true;
$include_calendar = false;
$include_template = 'template_main.php';
define('PAGE_TITLE', BOX_PHREEFORM_MODULE_ADM);

?>
