<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |

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
//  Path: /modules/rma/custom/pages/main/extra_actions.php
//
// This file contains the extra actions added to the maintain inventory module, it is executed
// before the standard switch statement
$pps_init_date     = $_POST['pps_init_date']     ? gen_db_date($_POST['pps_init_date'])     : '';
$pps_recharge_date = $_POST['pps_recharge_date'] ? gen_db_date($_POST['pps_recharge_date']) : '';
$pps_test_date     = $_POST['pps_test_date']     ? gen_db_date($_POST['pps_test_date'])     : '';
$pps_contact_date  = $_POST['pps_contact_date']  ? gen_db_date($_POST['pps_contact_date'])  : '';
$pps_disp_date     = $_POST['pps_disp_date']     ? gen_db_date($_POST['pps_disp_date'])     : '';

switch ($action) {
  case 'save': // this is an extra action for pps rma evaluation
	$id = db_prepare_input($_POST['id']);
	if (!$id) break; // need to have the record present to save
	$pps['pps_init_results']     = db_prepare_input($_POST['pps_init_results']);
	$pps['pps_init_by']          = db_prepare_input($_POST['pps_init_by']);
	$pps['pps_init_date']        = $pps_init_date;
	$pps['pps_recharge_req']     = db_prepare_input($_POST['pps_recharge_req']);
	$pps['pps_recharge_success'] = db_prepare_input($_POST['pps_recharge_success']);
	$pps['pps_recharge_failed']  = db_prepare_input($_POST['pps_recharge_failed']);
	$pps['pps_recharge_by']      = db_prepare_input($_POST['pps_recharge_by']);
	$pps['pps_recharge_date']    = $pps_recharge_date;
	$pps['pps_test_notes']       = db_prepare_input($_POST['pps_test_notes']);
	$pps['pps_test_by']          = db_prepare_input($_POST['pps_test_by']);
	$pps['pps_test_date']        = $pps_test_date;
	$pps['pps_contact_code']     = db_prepare_input($_POST['pps_contact_code']);
	$pps['pps_contact_by']       = db_prepare_input($_POST['pps_contact_by']);
	$pps['pps_contact_date']     = $pps_contact_date;
	$pps['pps_contact_notes']    = db_prepare_input($_POST['pps_contact_notes']);
	$pps['pps_disp_code']        = db_prepare_input($_POST['pps_disp_code']);
	$pps['pps_disp_by']          = db_prepare_input($_POST['pps_disp_by']);
	$pps['pps_disp_date']        = $pps_disp_date;
	db_perform(TABLE_RMA, array('pps' => serialize($pps)), 'update', 'id = '.$id);
	break;
  case 'edit': // load the evaluation data
    $id = isset($_POST['rowSeq']) ? $_POST['rowSeq'] : $_GET['cID'];
    $result = $db->Execute("select pps from " . TABLE_RMA . " where id = " . $id);
	$pps = unserialize($result->fields['pps']);
	break;
  default:
}

$pps_recharge_codes = array(
  '0' => 'Select ...',
  '1' => 'Open Cell',
  '2' => 'Shorted Cell',
  '3' => 'Voltage Too Low',
  '4' => 'Won\'t Hold Charge',
);
$pps_contact_codes = array(
 '0' => 'Select ...',
 '1' => 'Spoke with by phone',
 '2' => 'Left phone message',
 '3' => 'Direct email',
 '4' => 'Amazon email',
 '5' => 'Phone message & email',
 '6' => 'Spoke with in person',
);
$pps_disp_codes = array(
 '0' => 'Select ...',
 '1' => 'Top charge and return',
 '2' => 'Drain & scrap',
 '3' => 'Warranty to manufacturer',
 '5' => 'Returned To Stock',
);

$cal_init_date = array(
  'fieldname' => 'pps_init_date',
  'default'   => isset($pps['pps_init_date']) ? gen_locale_date($pps['pps_init_date']) : gen_locale_date($pps_init_date),
);
$cal_recharge_date = array(
  'fieldname' => 'pps_recharge_date',
  'default'   => isset($pps['pps_recharge_date']) ? gen_locale_date($pps['pps_recharge_date']) : gen_locale_date($pps_recharge_date),
);
$cal_test_date = array(
  'fieldname' => 'pps_test_date',
  'default'   => isset($pps['pps_test_date']) ? gen_locale_date($pps['pps_test_date']) : gen_locale_date($pps_test_date),
);
$cal_test_date = array(
  'fieldname' => 'pps_test_date',
  'default'   => isset($pps['pps_test_date']) ? gen_locale_date($pps['pps_test_date']) : gen_locale_date($pps_test_date),
);
$cal_contact_date = array(
  'fieldname' => 'pps_contact_date',
  'default'   => isset($pps['pps_contact_date']) ? gen_locale_date($pps['pps_contact_date']) : gen_locale_date($pps_contact_date),
);
$cal_disp_date = array(
  'fieldname' => 'pps_disp_date',
  'default'   => isset($pps['pps_disp_date']) ? gen_locale_date($pps['pps_disp_date']) : gen_locale_date($pps_disp_date),
);

?>