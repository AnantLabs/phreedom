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
//  Path: /modules/phreepos/pages/closing/pre_process.php
//
$security_level = validate_user(SECURITY_ID_POS_CLOSING);
define('JOURNAL_ID','30');
/**************  include page specific files    *********************/
require_once(DIR_FS_WORKING . 'classes/tills.php');
/**************   page specific initialization  *************************/
$action          = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
$payment_modules = load_all_methods('payment');
$tills           = new tills();
foreach ($payment_modules as $pmt_class) {
	$class  = $pmt_class['id'];
	$$class = new $class;
}
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/closing/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
	case 'save':
		break;
  	default:
}

/*****************   prepare to display templates  *************************/
if($tills->showDropDown() == false && $action=='') $action = 'one_till';
// build the display options

$include_header   = true;
$include_footer   = true;
$include_tabs     = false;
$include_calendar = true;
$include_template = 'template_main.php';
switch ($action) {
	case 'till_change':
	case 'one_till':
		if($tills->showDropDown() == false){
  			$tills->get_default_till_info();	
  		}else{
  			if(db_prepare_input($_POST['till_id'])==0) break;
		 	$tills->get_till_info(db_prepare_input($_POST['till_id']));
  		}
  		$field_list = array('main.shipper_code as description', 'SUM(item.debit_amount) as amount', 'main.journal_id', 'main.currencies_code', 'main.currencies_value');
		// 	hook to add new fields to the query return results
		/*$sql =  "select " . implode(', ', $field_list) . " from " . TABLE_JOURNAL_ITEM . " as item
			where main.journal_id in (19,21) and main.closed = '0' and item.gl_acct_id = " . $tills->gl_acct_id . " group by main.shipper_code, main.currencies_code";
  		*/
		if (is_array($extra_query_list_fields) > 0) $field_list = array_merge($field_list, $extra_query_list_fields);
		$query_raw = "select " . implode(', ', $field_list) . " from " . TABLE_JOURNAL_MAIN . " as main Join " . TABLE_JOURNAL_ITEM . " as item ON (main.id = item.ref_id) 
			where main.journal_id in (19,21) and main.closed = '0' and item.gl_account = " . $tills->gl_acct_id . " group by main.shipper_code, main.currencies_code";
		$query_result = $db->Execute($query_raw);
		$i = 0;
  		While(!$query_result->EOF){
  			$fields[$i]['PaymentMethod']  = $query_result->fields['description'];
  			$fields[$i]['AmountShouldBe'] = $currencies->format_full($query_result->fields['amount'], true, $query_result->fields['currencies_code'], $query_result->fields['currencies_value']);
  			$fields[$i]['Currency'] 	  = $query_result->fields['currencies_code'];
  			$i++;
  			$query_result->MoveNext();
  		}
  		//search for expences and other incomes.
  		$second_field_list = array( '( item.debit_amount - item.credit_amount ) as amount', 'item.description as description','main.journal_id', 'main.currencies_code', 'main.currencies_value');
		// 	hook to add new fields to the query return results
		if (is_array($extra_query_second_list_fields) > 0) $second_field_list = array_merge($second_field_list, $extra_query_list_fields);
		$query_raw = "select " . implode(', ', $second_field_list) . " from " . TABLE_JOURNAL_MAIN . " as main LEFT JOIN " . TABLE_JOURNAL_ITEM . " as item ON (main.id = item.ref_id) 
			where main.journal_id NOT IN (19,21) and main.closed = '0' and item.gl_account = " . $tills->gl_acct_id ;
		$query_result = $db->Execute($query_raw);print($query_raw);
  		While(!$query_result->EOF){
  			$fields[$i]['PaymentMethod']  = $query_result->fields['description'];
  			$fields[$i]['AmountShouldBe'] = $currencies->format_full($query_result->fields['amount'], true, $query_result->fields['currencies_code'], $query_result->fields['currencies_value']);
  			$fields[$i]['Currency'] 	  = $query_result->fields['currencies_code'];
  			$i++;
  			$query_result->MoveNext();
  		}
		
  	default: 
  
}

?>