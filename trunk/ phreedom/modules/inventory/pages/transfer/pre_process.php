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
//  Path: /modules/inventory/pages/transfer/pre_process.php
//
$security_level = validate_user(SECURITY_ID_TRANSFER_INVENTORY);
/**************  include page specific files    *********************/
gen_pull_language('phreebooks');
require_once(DIR_FS_WORKING . 'defaults.php');
require_once(DIR_FS_WORKING . 'functions/inventory.php');
require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
require_once(DIR_FS_MODULES . 'phreebooks/classes/gen_ledger.php');
/**************   page specific initialization  *************************/
define('JOURNAL_ID',16);	// Adjustment Journal
define('GL_TYPE', '');
$error     = false;
$post_date = ($_POST['post_date']) ? gen_db_date($_POST['post_date']) : date('Y-m-d');
$period    = gen_calculate_period($post_date);
if (!$period) $error = true;
$action    = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/transfer/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'save':
	if ($security_level < 2) { // security check
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	}
	// retrieve and clean input values
	$source_store_id = $_POST['source_store_id'];
	$dest_store_id   = $_POST['dest_store_id'];
	$skus = array();
	$i    = 1;
	while (true) {
	  if (!isset($_POST['sku_'.$i])) break;
	  $sku   = db_prepare_input($_POST['sku_'.$i]);
	  $qty   = db_prepare_input($_POST['qty_'.$i]);
	  $stock = db_prepare_input($_POST['stock_'.$i]);
	  if ($stock < $qty) {
	    $error = $messageStack->add(sprintf(INV_XFER_ERROR_NOT_ENOUGH_SKU, $sku), 'error');
		$qty = 0;
	  }
	  if ($qty > 0 && $sku <> '' && $sku <> TEXT_SEARCH) {
	    $skus[] = array(
		  'qty'     => $qty,
		  'serial'  => db_prepare_input($_POST['serial_'.$i]),
		  'sku'     => $sku,
		  'desc'    => db_prepare_input($_POST['desc_'.$i]),
		  'gl_acct' => db_prepare_input($_POST['acct_'.$i]),
	    );
	  }
	  $i++;
	}
	// test for errors
	if ($source_store_id == $dest_store_id) $error = $messageStack->add(INV_XFER_ERROR_SAME_STORE_ID, 'error');
	// process the request, first subtract from the source store
	if (!$error) {
	  // *************** START TRANSACTION *************************
	  $db->transStart();
	  foreach ($skus as $oneline) {
	    $sku    = $oneline['sku'];
		$qty    = $oneline['qty'];
		$serial = $oneline['serial'];
		$desc   = $oneline['desc'];
		$acct   = $oneline['gl_acct'];
	    $glEntry                      = new journal();
	    $glEntry->id                  = ''; // all transactions are considered new
	    $glEntry->journal_id          = JOURNAL_ID;
	    $glEntry->post_date           = $post_date;
	    $glEntry->period              = $period;
	    $glEntry->store_id            = $source_store_id;
	    $glEntry->admin_id            = $_SESSION['admin_id'];
	    $glEntry->purchase_invoice_id = db_prepare_input($_POST['purchase_invoice_id']);
	    $glEntry->closed              = '1'; // closes by default

	    $glEntry->journal_main_array  = $glEntry->build_journal_main_array();
	    $glEntry->journal_rows[]      = array(
		  'sku'              => $sku,
		  'qty'              => -$qty,
		  'gl_type'          => 'adj',
		  'serialize_number' => $serial,
		  'gl_account'       => $acct,
		  'description'      => $desc,
		  'credit_amount'    => '',
	    );
	    $glEntry->journal_rows[]      = array(
		  'sku'              => '',
		  'qty'              => '',
		  'gl_type'          => 'ttl',
		  'gl_account'       => $acct,
		  'description'      => sprintf(INV_LOG_TRANSFER, $source_store_id, $dest_store_id),
		  'debit_amount'     => '',
	    );
	    if (!$glEntry->Post('insert')) $error = true;
	    // Extract the cost to use as the total amount for the next adjustment
	    foreach ($glEntry->journal_rows as $value) {
	      if ($value['gl_type'] == 'cog') $tot_amount = $value['credit_amount'] + $value['debit_amount'];
	    }
	    // now make another adjustment to the new store (treat like purchase/receive)
	    $glEntry                      = new journal();
	    $glEntry->id                  = ''; // all transactions are considered new
	    $glEntry->journal_id          = JOURNAL_ID;
	    $glEntry->post_date           = $post_date;
	    $glEntry->period              = $period;
	    $glEntry->store_id            = $dest_store_id;
	    $glEntry->admin_id            = $_SESSION['admin_id'];
	    $glEntry->purchase_invoice_id = db_prepare_input($_POST['purchase_invoice_id']);
	    $glEntry->closed              = '1'; // closes by default
	    $glEntry->journal_main_array  = $glEntry->build_journal_main_array();
	    $glEntry->journal_main_array['total_amount'] = $tot_amount;
	    $glEntry->journal_rows[] = array(
		  'sku'              => $sku,
		  'qty'              => $qty,
		  'gl_type'          => 'adj',
		  'serialize_number' => $serial,
		  'gl_account'       => $acct,
		  'description'      => $desc,
		  'debit_amount'     => $tot_amount,
	    );
	    $glEntry->journal_rows[] = array(
		  'sku'              => '',
		  'qty'              => '',
		  'gl_type'          => 'ttl',
		  'gl_account'       => $acct,
		  'description'      => sprintf(INV_LOG_TRANSFER, $source_store_id, $dest_store_id),
		  'credit_amount'    => $tot_amount,
	    );
	    if (!$glEntry->Post('insert')) $error = true;
	  }
	  if ($error) {
		if (DEBUG) $messageStack->write_debug();
	    $db->transRollback();
		break;
	  } else {
		$db->transCommit();
		gen_add_audit_log(sprintf(INV_LOG_TRANSFER, $source_store_id, $dest_store_id), $sku, $qty);
		$messageStack->add_session(sprintf(INV_XFER_SUCCESS, $qty, $sku),'success');
		if (DEBUG) $messageStack->write_debug();
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		// *************** END TRANSACTION *************************
	  }
	}
	$messageStack->add(GL_ERROR_NO_POST, 'error');
	$cInfo = new objectInfo($_POST);
	break;
  case 'delete':
  case 'edit':
  default:
	$cInfo = new objectInfo(array());
}
/*****************   prepare to display templates  *************************/
$gl_array_list = gen_coa_pull_down();
$cal_xfr = array(
  'name'      => 'dateReference',
  'form'      => 'inv_xfer',
  'fieldname' => 'post_date',
  'imagename' => 'btn_date_1',
  'default'   => gen_locale_date($post_date),
);
$include_header   = true;
$include_footer   = true;
$include_calendar = true;
$include_template = 'template_main.php';
define('PAGE_TITLE', BOX_INV_TRANSFER);

?>