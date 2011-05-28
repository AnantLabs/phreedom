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
//  Path: /modules/phreebooks/pages/admin_tools/pre_process.php
//
$security_level = validate_user(SECURITY_ID_GEN_ADMIN_TOOLS);
/**************  include page specific files    *********************/
gen_pull_language($module, 'admin');
require(DIR_FS_WORKING . 'functions/phreebooks.php');
require(DIR_FS_WORKING . 'classes/gen_ledger.php');
/**************   page specific initialization  *************************/
define('JOURNAL_ID',2);	// General Journal
$error      = false;
$action     = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
$start_date = ($_POST['start_date'])  ? gen_db_date($_POST['start_date']) : CURRENT_ACCOUNTING_PERIOD_START;
$end_date   = ($_POST['end_date'])    ? gen_db_date($_POST['end_date'])   : CURRENT_ACCOUNTING_PERIOD_END;
$action     = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
// see what fiscal year we are looking at (assume this FY is entered for the first time)
if ($_POST['fy']) {
  $fy = $_POST['fy'];
} else {
  $result = $db->Execute("select fiscal_year from " . TABLE_ACCOUNTING_PERIODS . " where period = " . CURRENT_ACCOUNTING_PERIOD);
  $fy = $result->fields['fiscal_year'];
}
// find the highest posted period to disallow accounting period changes
$result     = $db->Execute("select max(period) as period from " . TABLE_JOURNAL_MAIN);
$max_period = ($result->fields['period'] > 0) ? $result->fields['period'] : 0;
// find the highest fiscal year and period in the system
$result     = $db->Execute("select max(fiscal_year) as fiscal_year, max(period) as period from " . TABLE_ACCOUNTING_PERIODS);
$highest_fy = ($result->fields['fiscal_year'] > 0) ? ($result->fields['fiscal_year']) : '';
$highest_period = ($result->fields['period'] > 0) ? ($result->fields['period']) : '';
$period     = CURRENT_ACCOUNTING_PERIOD;
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/admin_tools/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'update':
	if ($security_level < 3) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	}
	// propagate into remaining fiscal years if the last date was changed.
	$fy_array = array();
	$x = 0;
	while(isset($_POST['start_' . $x])) {
		$update_period = db_prepare_input($_POST['per_' . $x]);
		$fy_array = array(
			'start_date' => gen_db_date(db_prepare_input($_POST['start_' . $x])),
			'end_date' => gen_db_date(db_prepare_input($_POST['end_' . $x])));
		db_perform(TABLE_ACCOUNTING_PERIODS, $fy_array, 'update', 'period = ' . (int)$update_period);
		$x++;
	}
	// see if there is a disconnect between fiscal years
	$next_period = $update_period + 1;
	$next_start_date = date('Y-m-d', strtotime($fy_array['end_date']) + (60 * 60 * 24));
	$result = $db->Execute("select start_date from " . TABLE_ACCOUNTING_PERIODS . " where period = " . $next_period);
	if ($result->RecordCount() > 0) { // next FY exists, check it
		if ($next_start_date <> $result->fields['start_date']) {
			$fy_array = array('start_date' =>$next_start_date);
			db_perform(TABLE_ACCOUNTING_PERIODS, $fy_array, 'update', 'period = ' . (int)$next_period);
			$messageStack->add(GL_ERROR_FISCAL_YEAR_SEQ, 'caution');
			$fy++;
		}
	}
	gen_add_audit_log(GL_LOG_FY_UPDATE . TEXT_UPDATE);
	break;
  case 'new':
	if ($security_level < 2) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	$result = $db->Execute("select * from " . TABLE_ACCOUNTING_PERIODS . " where period = " . $highest_period);
	$next_fy         = $result->fields['fiscal_year'] + 1;
	$next_period     = $result->fields['period'] + 1;
	$next_start_date = date('Y-m-d', strtotime($result->fields['end_date']) + (60 * 60 * 24));
	$highest_period  = validate_fiscal_year($next_fy, $next_period, $next_start_date);
	build_and_check_account_history_records();
	// *************** roll account balances into next fiscal year *************************
    $glEntry = new journal();
	$result = $db->Execute("select id from " . TABLE_CHART_OF_ACCOUNTS);
	while (!$result->EOF) {
		$glEntry->affected_accounts[$result->fields['id']] = 1;
		$result->MoveNext();
	}
	$glEntry->update_chart_history_periods(CURRENT_ACCOUNTING_PERIOD); // from current period through new fiscal year
	$fy = $next_fy;	// set the pointer to open the fiscal year added
	gen_add_audit_log(GL_LOG_FY_UPDATE . TEXT_ADD);
	break;
  case "change":
	// retrieve the desired period and update the system default values.
	if ($security_level < 3) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	$period = (int)db_prepare_input($_POST['period']);
	if ($period <= 0 || $period > $highest_period) {
		$messageStack->add(GL_ERROR_BAD_ACCT_PERIOD, 'error');
		break;
	}
	$result = $db->Execute("select start_date, end_date from " . TABLE_ACCOUNTING_PERIODS . " where period = " . $period);
	$db->Execute("update " . TABLE_CONFIGURATION . " set configuration_value = " . $period . " 
		where configuration_key = 'CURRENT_ACCOUNTING_PERIOD'");
	$db->Execute("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $result->fields['start_date'] . "' 
		where configuration_key = 'CURRENT_ACCOUNTING_PERIOD_START'");
	$db->Execute("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $result->fields['end_date'] . "' 
		where configuration_key = 'CURRENT_ACCOUNTING_PERIOD_END'");
	gen_add_audit_log(GEN_LOG_PERIOD_CHANGE);
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	break;
  case 'beg_balances': // Enter beginning balances
	gen_redirect(html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=beg_bal', 'SSL'));
	break;
  case 'purge_db':
	if ($security_level < 4) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	if ($_POST['purge_confirm'] == 'purge') {
		$db->Execute("TRUNCATE TABLE " . TABLE_JOURNAL_MAIN);
		$db->Execute("TRUNCATE TABLE " . TABLE_JOURNAL_ITEM);
		$db->Execute("TRUNCATE TABLE " . TABLE_ACCOUNTS_HISTORY);
		$db->Execute("TRUNCATE TABLE " . TABLE_INVENTORY_HISTORY);
		$db->Execute("TRUNCATE TABLE " . TABLE_INVENTORY_COGS_OWED);
		$db->Execute("TRUNCATE TABLE " . TABLE_INVENTORY_COGS_USAGE);
		$db->Execute("TRUNCATE TABLE " . TABLE_RECONCILIATION);
		$db->Execute("TRUNCATE TABLE " . TABLE_SHIPPING_LOG);
		$db->Execute("update " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " set beginning_balance = 0, debit_amount = 0, credit_amount = 0");
		$db->Execute("update " . TABLE_INVENTORY . " set quantity_on_hand = 0, quantity_on_order = 0, quantity_on_sales_order = 0");
		$messageStack->add_session(GL_UTIL_PURGE_CONFIRM, 'success');
	} else {
		$messageStack->add_session(GL_UTIL_PURGE_FAIL, 'caution');
	}
	gen_add_audit_log(GL_LOG_PURGE_DB);
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	break;
  case 'repost':
	if ($security_level < 4) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	// determine which journals were selected to re-post
	$valid_journals = array(2,3,4,6,7,8,9,10,12,13,14,16,18,19,20,21,22);
	$journals = array();
	foreach ($valid_journals as $journal_id) if (isset($_POST['jID_' . $journal_id])) $journals[] = $journal_id;
	$repost_cnt = repost_journals($journals, $start_date, $end_date);
	if ($repost_cnt === false) {
	  $messageStack->add(GEN_ADM_TOOLS_RE_POST_FAILED,'caution');
	} else {
	  $messageStack->add(sprintf(GEN_ADM_TOOLS_RE_POST_SUCCESS, $repost_cnt),'success');
	  gen_add_audit_log(GEN_ADM_TOOLS_AUDIT_LOG_RE_POST, implode(',', $journals));
	}
	if (DEBUG) $messageStack->write_debug();
	break;

  case 'coa_hist_test':
  case 'coa_hist_fix':
	if ($security_level < 4) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	$fiscal_years = array();
	$sql = "select distinct fiscal_year, min(period) as first_period, max(period) as last_period
	  from " . TABLE_ACCOUNTING_PERIODS . " group by fiscal_year order by fiscal_year ASC";
	$result = $db->Execute($sql);
	while (!$result->EOF) {
	  $fiscal_years[] = array(
	    'fiscal_year'  => $result->fields['fiscal_year'],
		'first_period' => $result->fields['first_period'],
		'last_period'  => $result->fields['last_period']);
	  $result->MoveNext();
	}
	$result = $db->Execute("select id from " . TABLE_CHART_OF_ACCOUNTS . " where account_type = 44");
	$retained_earnings_acct = $result->fields['id'];

	$beg_bal      = array();
	$bad_accounts = array();
	foreach ($fiscal_years as $fiscal_year) {
	  $sql = "select account_id, period, beginning_balance, (beginning_balance + debit_amount - credit_amount) as next_beg_bal
		from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " 
		where period >= " . $fiscal_year['first_period'] . " and period <= " . $fiscal_year['last_period'] . " 
		order by period, account_id";
	  $result = $db->Execute($sql);
	  while (!$result->EOF) {
	    if ($result->fields['account_id'] == $retained_earnings_acct) { // skip rounding account since it can change
		  $result->MoveNext();
		  continue;
		}
		$period       = $result->fields['period'];
		$next_period  = $period + 1;
		$gl_account   = $result->fields['account_id'];
		$beg_balance  = $currencies->format($result->fields['beginning_balance']);
		$next_beg_bal = $currencies->format($result->fields['next_beg_bal']);
		$beg_bal[$next_period][$gl_account] = $next_beg_bal;
		if ($period <> 1 && $beg_bal[$period][$gl_account] <> $beg_balance) {
		  if ($action <> 'coa_hist_fix') $messageStack->add(sprintf(GEN_ADM_TOOLS_REPAIR_ERROR_MSG, $period, $gl_account, $beg_bal[$period][$gl_account], $beg_balance),'caution');
		  $bad_accounts[$period][$gl_account] = array('sync' => '1');
		}
		// check posted transactions to account to see if they match
		$posted = $db->Execute("select sum(debit_amount) as debit, sum(credit_amount) as credit 
		  from " . TABLE_JOURNAL_MAIN . " m join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id
		  where period = " . $period . " and gl_account = '" . $gl_account . "' 
		  and journal_id in (2, 6, 7, 12, 13, 14, 16, 18, 19, 20, 21)");
		$posted_bal   = $currencies->format($result->fields['beginning_balance'] + $posted->fields['debit'] - $posted->fields['credit']);
		if ($posted_bal <> $next_beg_bal) {
		  if ($action <> 'coa_hist_fix') $messageStack->add(sprintf(GEN_ADM_TOOLS_REPAIR_ERROR_MSG, $period, $gl_account, $posted_bal, $next_beg_bal),'caution');
		  $bad_accounts[$period][$gl_account] = array(
		    'sync'   => '1',
		    'debit'  => $posted->fields['debit'],
		    'credit' => $posted->fields['credit'],
		  );
		}
		$result->MoveNext();
	  }
	  // roll the fiscal year balances
	  // select list of accounts that need to be closed, adjusted
	  $sql = "select id from " . TABLE_CHART_OF_ACCOUNTS . " where account_type in (30, 32, 34, 42, 44)";
	  $result = $db->Execute($sql);
	  $acct_list = array();
	  while(!$result->EOF) {
		$beg_bal[$next_period][$result->fields['id']] = 0;
		$acct_list[] = $result->fields['id'];
		$result->MoveNext();
	  }
	  // fetch the totals for the closed accounts
	  $sql = "select sum(beginning_balance + debit_amount - credit_amount) as retained_earnings 
		from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " 
		where account_id in ('" . implode("','",$acct_list) . "') and period = " . $period;
	  $result = $db->Execute($sql);
	  $beg_bal[$next_period][$retained_earnings_acct] = $currencies->format($result->fields['retained_earnings']);
	}
	if ($action == 'coa_hist_fix') {
	  // find the affected accounts
	  if (sizeof($bad_accounts) > 0) {
		// *************** START TRANSACTION *************************
		$db->transStart();
	    $glEntry = new journal();
		$min_period = 999999;
		foreach ($bad_accounts as $period => $acct_array) {
		  foreach ($acct_array as $gl_acct => $value) {
			$min_period = min($period, $min_period); // find first period that has an error
			$glEntry->affected_accounts[$gl_acct] = 1;
			if (isset($value['debit'])) { // the history doesn't match posted data, repair
			  $db->Execute("update " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " 
			    set debit_amount = " . $value['debit'] . ", credit_amount = " . $value['credit'] . " 
			    where period = " . $period . " and account_id = '" . $gl_acct . "'");
			}
		  }
		}
		$min_period = max($min_period, 2); // avoid a crash if min_period is the first period
		if ($glEntry->update_chart_history_periods($min_period - 1)) { // from prior period than the error account
			$db->transCommit();
			$messageStack->add_session(GEN_ADM_TOOLS_REPAIR_COMPLETE,'success');
			gen_add_audit_log(GEN_ADM_TOOLS_REPAIR_LOG_ENTRY);
//			gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')) . 'action=coa_hist_test', 'SSL'));
		}
	  }
	}
	if (sizeof($bad_accounts) == 0) {
	  $messageStack->add(GEN_ADM_TOOLS_REPAIR_SUCCESS,'success');
	} else {
	  $messageStack->add(GEN_ADM_TOOLS_REPAIR_ERROR,'error');
	}
	if (DEBUG) $messageStack->write_debug();
    break;

  default:
}

/*****************   prepare to display templates  *************************/
$result = $db->Execute("select period, start_date, end_date from " . TABLE_ACCOUNTING_PERIODS . " where fiscal_year = " . $fy);
$fy_array = array();
while(!$result->EOF) {
  $fy_array[$result->fields['period']] = array('start' => $result->fields['start_date'], 'end' => $result->fields['end_date']);
  $result->MoveNext();
}

$cal_start = array(
  'name'      => 'startDate',
  'form'      => 'admin_tools',
  'fieldname' => 'start_date',
  'imagename' => 'btn_date_1',
  'default'   => isset($start_date) ? gen_locale_date($start_date) : date(DATE_FORMAT),
  'params'    => array('align' => 'left'),
);
$cal_end = array(
  'name'      => 'endDate',
  'form'      => 'admin_tools',
  'fieldname' => 'end_date',
  'imagename' => 'btn_date_2',
  'default'   => isset($end_date) ? gen_locale_date($end_date) : date(DATE_FORMAT),
  'params'    => array('align' => 'left'),
);

$include_header   = true;
$include_footer   = true;
$include_tabs     = false;
$include_calendar = true;
$include_template = 'template_main.php';
define('PAGE_TITLE', BOX_HEADING_ADMIN_TOOLS);

?>