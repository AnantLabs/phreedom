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
//  Path: /modules/contacts/pages/main/pre_process.php
//

/**************   page specific initialization  *************************/
$error       = false;
$contact_js  = '';
$js_pmt_array= '';
$js_actions  = '';
$criteria    = array();
$search_text = $_POST['search_text'] ? db_input($_POST['search_text']) : db_input($_GET['search_text']);
if ($search_text == TEXT_SEARCH) $search_text = '';
$action      = isset($_GET['action']) ? $_GET['action'] : $_POST['todo'];
if (!$action && $search_text <> '') $action = 'search'; // if enter key pressed and search not blank
$type        = isset($_GET['type']) ? $_GET['type'] : 'c'; // default to customer
// load the filters
$f0 = isset($_POST['todo']) ? (isset($_POST['f0']) ? $_POST['f0'] : '0') : (isset($_GET['f0']) ? $_GET['f0'] : '1'); // show inactive checkbox
$_GET['f0'] = $f0;
/**************   Check user security   *****************************/
switch ($type) {
  case 'c': // customers
	$terms_type     = 'AR';
	$security_token = SECURITY_ID_MAINTAIN_CUSTOMERS;
	$page_title_new = BOX_CONTACTS_NEW_CUSTOMER;
	$auto_type      = AUTO_INC_CUST_ID;
	$auto_field     = 'next_cust_id_num';
	break;
  case 'v': // vendors
	$terms_type     = 'AP';
	$security_token = SECURITY_ID_MAINTAIN_VENDORS;
	$page_title_new = BOX_CONTACTS_NEW_VENDOR;
	$auto_type      = AUTO_INC_VEND_ID;
	$auto_field     = 'next_vend_id_num';
	break;
  case 'i': // crm
	$terms_type     = 'AP'; // not really used, just keeps errors down
	$security_token = SECURITY_ID_PHREECRM;
	$page_title_new = BOX_CONTACTS_NEW_CONTACT;
	$auto_type      = '';
	$auto_field     = '';
	break;
  case 'e': // employees
	$terms_type     = 'AP'; // not really used, just keeps errors down
	$security_token = SECURITY_ID_MAINTAIN_EMPLOYEES;
	$page_title_new = BOX_CONTACTS_NEW_EMPLOYEE;
	$auto_type      = false;
	$auto_field     = '';
	break;
  case 'b': // branches
	$terms_type     = 'AP'; // not really used, just keeps errors down
	$security_token = SECURITY_ID_MAINTAIN_BRANCH;
	$page_title_new = BOX_CONTACTS_NEW_BRANCH;
	$auto_type      = false;
	$auto_field     = '';
	break;
  case 'j': // jobs/projects
	$terms_type     = 'AP'; // not really used, just keeps errors down
	$security_token = SECURITY_ID_MAINTAIN_PROJECTS;
	$page_title_new = BOX_CONTACTS_NEW_PROJECT;
	$auto_type      = false;
	$auto_field     = '';
	break;
  default:
}
/***************   hook for custom security  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/contacts/main/extra_security.php';
if (file_exists($custom_path)) { include($custom_path); }
$security_level = validate_user($security_token);
/**************  include page specific files    *********************/
require_once(DIR_FS_WORKING . 'defaults.php');
require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
require_once(DIR_FS_WORKING . 'functions/contacts.php');
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/main/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }

/***************   Act on the action request   *************************/
switch ($action) {
  case 'new':
	if ($security_level < 2) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	$contacts = '';
	$cInfo    = new objectInfo(array('id'=>''));
	break;

  case 'save':
	$id             = (int)db_prepare_input($_POST['id']);  // if present, then its an edit
	if ($security_level < 2 || ($id && $security_level < 3)) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
		break;
	}
	$short_name     = db_prepare_input($_POST['short_name']);
	$inc_auto_id    = false;
	if ($auto_type && !$short_name) {
		$result = $db->Execute("select " . $auto_field . " from " . TABLE_CURRENT_STATUS);
		$short_name = $result->fields[$auto_field];
		$inc_auto_id = true;
	}
	$inactive       = isset($_POST['inactive']) ? '1' : '0';
	$contact_first  = db_prepare_input($_POST['contact_first']);
	$contact_middle = db_prepare_input($_POST['contact_middle']);
	$contact_last   = db_prepare_input($_POST['contact_last']);
	$store_id       = db_prepare_input($_POST['store_id']);
	$gov_id_number  = db_prepare_input($_POST['gov_id_number']);
	$dept_rep_id    = db_prepare_input($_POST['dept_rep_id']);
	$payment_id     = db_prepare_input($_POST['payment_id']);
	$account_number = db_prepare_input($_POST['account_number']);
	$price_sheet    = db_prepare_input($_POST['price_sheet']);
	$tax_id         = db_prepare_input($_POST['tax_id']);
	if (is_array($_POST['gl_type_account'])) {
	  $gl_type_account = implode('', array_keys($_POST['gl_type_account']));
	} else {
	  $gl_type_account = db_prepare_input($_POST['gl_type_account']);
	}

	// terms related fields
	$special_terms = db_prepare_input($_POST['special_terms']);
	if ($special_terms == 'on') $special_terms = '0'; // special case for value = 0 in IE
	$early_percent = db_prepare_input($_POST['early_percent']);
	$early_days    = db_prepare_input($_POST['early_days']);
	$standard_days = db_prepare_input($_POST['standard_days']);
	$due_date      = db_prepare_input($_POST['due_date']);
	$credit_limit  = $currencies->clean_value(db_prepare_input($_POST['credit_limit']));
	$terms         = $early_percent . ':' . $early_days . ':';
	if ($special_terms == 4 || $special_terms == 5) {
		$terms .= $due_date;
	} else {
		$terms .= $standard_days;
	}
	$special_terms = $special_terms . ':' . $terms . ':' . $credit_limit;

	if ($security_level < 2) {
		$messageStack->add(ERROR_NO_PERMISSION,'error');
		$action = ($action == 'save') ? 'new' : 'update';
		$_POST['special_terms'] = $special_terms; // reload imploded terms to prepare for reload
		$cInfo = new objectInfo($_POST);
		break;
	}
	// check to encrypt payment information (needs to be set up before writing address book
	if (ENABLE_ENCRYPTION && $_POST['payment_cc_name'] && $_POST['payment_cc_number']) { // save payment info
	  if (strlen($_SESSION['admin_encrypt']) > 1) {
		$card_name = db_prepare_input($_POST['payment_cc_name']);
		$card_num  = db_prepare_input($_POST['payment_cc_number']);
		if ($card_num) {
			$card_num = preg_replace("/[^0-9]/", "", $card_num);
			$hint = substr($card_num, 0, 1);
			for ($a = 0; $a < (strlen($card_num) - 5); $a++) $hint .= '*'; 
			$hint .= substr($card_num, -4);
			$payment = array( // the sequence is important!
				$card_name,
				$card_num,
				db_prepare_input($_POST['payment_exp_month']),
				db_prepare_input($_POST['payment_exp_year']),
				db_prepare_input($_POST['payment_cc_cvv2']),
			);
			$val = implode(':', $payment) . ':';
			$encrypt = new encryption();
			if (!$enc_value = $encrypt->encrypt($_SESSION['admin_encrypt'], $val, 128)) {
				$messageStack->add('Encryption error - ' . implode('. ', $encrypt->errors), 'error');
				$error = true;
			}
			$encryption_array = array(
				'hint'      => $hint,
				'module'    => 'contacts',
				'enc_value' => $enc_value,
			);
		}
	  } else {
	    $error = $messageStack->add(ACT_NO_KEY_EXISTS,'error');
	  }
	}
	// address book fields
	$addresses = array();
	foreach ($address_types as $value) {
      if (($value <> 'im' && substr($value, 1, 1) == 'm') || // all main addresses except contacts which is optional
	      ($value == 'im' && $type == 'i') || // contact main address when editing the contact directly
		  ($_POST[$value . '_primary_name'] <> '')) { // optional billing, shipping, and contact
		$addresses[$value]['address_id']     = db_prepare_input($_POST[$value . '_address_id']);
		$addresses[$value]['delete']         = db_prepare_input($_POST[$value . '_delete']);
		$addresses[$value]['primary_name']   = db_prepare_input($_POST[$value . '_primary_name'], $required = true);
		$addresses[$value]['contact']        = db_prepare_input($_POST[$value . '_contact'], ADDRESS_BOOK_CONTACT_REQUIRED);
		$addresses[$value]['address1']       = db_prepare_input($_POST[$value . '_address1'], ADDRESS_BOOK_ADDRESS1_REQUIRED);
		$addresses[$value]['address2']       = db_prepare_input($_POST[$value . '_address2'], ADDRESS_BOOK_ADDRESS2_REQUIRED);
		$addresses[$value]['city_town']      = db_prepare_input($_POST[$value . '_city_town'], ADDRESS_BOOK_CITY_TOWN_REQUIRED);
		$addresses[$value]['state_province'] = db_prepare_input($_POST[$value . '_state_province'], ADDRESS_BOOK_STATE_PROVINCE_REQUIRED);
		$addresses[$value]['postal_code']    = db_prepare_input($_POST[$value . '_postal_code'], ADDRESS_BOOK_POSTAL_CODE_REQUIRED);
		$addresses[$value]['country_code']   = db_prepare_input($_POST[$value . '_country_code']);
		$addresses[$value]['telephone1']     = db_prepare_input($_POST[$value . '_telephone1'], ADDRESS_BOOK_TELEPHONE1_REQUIRED);
		$addresses[$value]['telephone2']     = db_prepare_input($_POST[$value . '_telephone2']);
		$addresses[$value]['telephone3']     = db_prepare_input($_POST[$value . '_telephone3']);
		$addresses[$value]['telephone4']     = db_prepare_input($_POST[$value . '_telephone4']);
		$addresses[$value]['email']          = db_prepare_input($_POST[$value . '_email'], ADDRESS_BOOK_EMAIL_REQUIRED);
		$addresses[$value]['website']        = db_prepare_input($_POST[$value . '_website']);
		$addresses[$value]['notes']          = db_prepare_input($_POST[$value . '_notes']);
		// error check
		$msg_add_type = constant('ACT_CATEGORY_' . strtoupper(substr($value, 1, 1)) . '_ADDRESS');
		if ($addresses[$value]['primary_name']   === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_PRIMARY_NAME,   'error');
		if ($addresses[$value]['contact']        === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_CONTACT,        'error');
		if ($addresses[$value]['address1']       === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_ADDRESS1,       'error');
		if ($addresses[$value]['address2']       === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_ADDRESS2,       'error');
		if ($addresses[$value]['city_town']      === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_CITY_TOWN,      'error');
		if ($addresses[$value]['state_province'] === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_STATE_PROVINCE, 'error');
		if ($addresses[$value]['postal_code']    === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_POSTAL_CODE,    'error');
		if ($addresses[$value]['telephone1']     === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_TELEPHONE1,     'error');
		if ($addresses[$value]['email']          === false) $error = $messageStack->add(GEN_ERRMSG_NO_DATA . $msg_add_type . ' - ' . GEN_EMAIL,          'error');
	  }
	}

	// check for duplicate short_name IDs
	if (!$id) {
	 $sql = "select distinct id from " . TABLE_CONTACTS . " 
		where short_name = '" . $short_name . "' and type = '" . $type . "'";
	} else { // $action == update
	  $sql = "select id from " . TABLE_CONTACTS . " 
		where short_name = '" . $short_name . "' and type = '" . $type . "' and id <> " . $id;
	}
	$result = $db->Execute($sql);
	if ($result->RecordCount() > 0) $error = $messageStack->add(ACT_ERROR_DUPLICATE_ACCOUNT,'error');

	// load the contact information
	if ($type <> 'i') {
	  $i_id             = db_prepare_input($_POST['i_id']);
	  $i_address_id     = db_prepare_input($_POST['i_address_id']);
	  $i_short_name     = db_prepare_input($_POST['i_short_name']);
	  $i_contact_middle = db_prepare_input($_POST['i_contact_middle']); // title
	  $i_contact_first  = db_prepare_input($_POST['i_contact_first']);
	  $i_contact_last   = db_prepare_input($_POST['i_contact_last']);
	  $i_gov_id_number  = db_prepare_input($_POST['i_gov_id_number']);
	  $i_account_number = db_prepare_input($_POST['i_account_number']);

	  // error check contact
	  if (!$i_id) {
		$sql = "select distinct id from " . TABLE_CONTACTS . " 
			where short_name = '" . $i_short_name . "' and type = 'i'";
	  } else { // $action == update
		$sql = "select id from " . TABLE_CONTACTS . " 
			where short_name = '" . $i_short_name . "' and type = 'i' and id <> " . $i_id;
	  }
	  $result = $db->Execute($sql);
	  if ($result->RecordCount() > 0) $error = $messageStack->add(ACT_ERROR_DUPLICATE_CONTACT,'error');
	  if ($addresses['im']['primary_name'] && !$i_short_name) {
	    $error = $messageStack->add(ACT_I_TYPE_NAME . ': ' . ACT_JS_SHORT_NAME,'error');
	  }

	}

	if ($error == false) {
	  $sql_data_array = array(
		'type'            => $type,
		'short_name'      => $short_name,
		'inactive'        => $inactive,
		'contact_first'   => $contact_first,
		'contact_middle'  => $contact_middle,
		'contact_last'    => $contact_last,
		'store_id'        => $store_id,
		'gl_type_account' => $gl_type_account,
		'gov_id_number'   => $gov_id_number,
		'dept_rep_id'     => $dept_rep_id,
		'account_number'  => $account_number,
		'special_terms'   => $special_terms,
		'price_sheet'     => $price_sheet,
		'tax_id'          => $tax_id,
		'last_update'     => 'now()',
	  );
	  $xtra_db_fields = $db->Execute("select field_name, entry_type, params 
	    from " . TABLE_EXTRA_FIELDS . " where tab_id > 0 and module_id='contacts'");
	  while (!$xtra_db_fields->EOF) {
	    $field_name = $xtra_db_fields->fields['field_name'];
	    if ($xtra_db_fields->fields['entry_type'] == 'multi_check_box') {
		  $temp ='';
	      $params = unserialize($xtra_db_fields->fields['params']);
		  $choices = explode(',',$params['default']);
	      while ($choice = array_shift($choices)) {
	        $values = explode(':',$choice);
		    If(isset($_POST[$field_name.$values[0]])){
			  $temp.= $_POST[$field_name.$values[0]].',';
			}}
		  $sql_data_array[$field_name] = $temp;
		}elseif (!isset($_POST[$field_name]) && $xtra_db_fields->fields['entry_type'] == 'check_box') {
		  $sql_data_array[$field_name] = '0'; // special case for unchecked check boxes
	    } elseif (isset($_POST[$field_name]) && $field_name <> 'id') {
		  $sql_data_array[$field_name] = db_prepare_input($_POST[$field_name]);
	    }
	    if ($xtra_db_fields->fields['entry_type'] == 'date_time') {
		  $sql_data_array[$field_name] = ($sql_data_array[$field_name]) ? gen_db_date($sql_data_array[$field_name]) : '';
	    }
	    $xtra_db_fields->MoveNext();
	  }
	  // special case for contacts
//	  if ($type == 'i') { // convert dept_rep_id to record id
//		$result = $db->Execute("select id from " . TABLE_CONTACTS . " where short_name = '" . $dept_rep_id . "'");
//		$sql_data_array['dept_rep_id'] = ($result->RecordCount() > 0) ? $result->fields['id'] : 0;
//	  }
	  if (!$id) { //create record
		$sql_data_array['first_date'] = 'now()';
		db_perform(TABLE_CONTACTS, $sql_data_array, 'insert');
		$id = db_insert_id();
		// if auto-increment see if the next id is there and increment if so.
		if ($inc_auto_id) { // increment the ID value
			$next_id = string_increment($short_name);
			$db->Execute("update " . TABLE_CURRENT_STATUS . " set " . $auto_field . " = '" . $next_id . "'");
		}
		gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_ADD . '-' . constant('ACT_' . strtoupper($type) . '_TYPE_NAME'), $short_name);
	  } else { // update record
		db_perform(TABLE_CONTACTS, $sql_data_array, 'update', "id = '" . $id . "'");
		gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_UPDATE . '-' . constant('ACT_' . strtoupper($type) . '_TYPE_NAME'), $short_name);
	  }

	  // contact main record
	  if ($type <> 'i' && $i_short_name) { // is null
	    $sql_data_array = array(
		  'type'            => 'i',
		  'short_name'      => $i_short_name,
		  'contact_first'   => $i_contact_first,
		  'contact_middle'  => $i_contact_middle,
		  'contact_last'    => $i_contact_last,
		  'gov_id_number'   => $i_gov_id_number,
		  'account_number'  => $i_account_number,
		  'dept_rep_id'     => $id,
		  'last_update'     => 'now()',
	    );
	    if (!$i_id) { //create record
		  $sql_data_array['first_date'] = 'now()';
		  db_perform(TABLE_CONTACTS, $sql_data_array, 'insert');
		  $i_id = db_insert_id();
	    } else { // update record
		  db_perform(TABLE_CONTACTS, $sql_data_array, 'update', "id = '" . $i_id . "'");
	    }
	  }

	  // address book fields
	  foreach ($addresses as $a_type => $value) {
		$sql_data_array = array(
		  'ref_id'         => $id,
		  'type'           => $a_type,
		  'primary_name'   => $value['primary_name'],
		  'contact'        => $value['contact'],
		  'address1'       => $value['address1'],
		  'address2'       => $value['address2'],
		  'city_town'      => $value['city_town'],
		  'state_province' => $value['state_province'],
		  'postal_code'    => $value['postal_code'],
		  'country_code'   => $value['country_code'],
		  'telephone1'     => $value['telephone1'],
		  'telephone2'     => $value['telephone2'],
		  'telephone3'     => $value['telephone3'],
		  'telephone4'     => $value['telephone4'],
		  'email'          => $value['email'],
		  'website'        => $value['website'],
		  'notes'          => $value['notes'],
		);
		if ($type <> 'i' && $a_type == 'im') $sql_data_array['ref_id'] = $i_id; // re-point contact
		if (!$value['address_id']) { // then it's a new address
		  db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'insert');
		  $value['address_id'] = db_insert_id();
		} else { // then update address
		  db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_id = '" . $value['address_id'] . "'");
		}
		if ($a_type <> 'im' && substr($a_type, 1, 1) == 'm' && $encryption_array) $main_add_id = $value['address_id']; // save for payment insert
	  }

	  // payment fields
	  if ($encryption_array) { // update the payment info
		$encryption_array['ref_1'] = $id;
		$encryption_array['ref_2'] = $main_add_id;
		if ($payment_id) {
		  db_perform(TABLE_DATA_SECURITY, $encryption_array, 'update', 'id = ' . $payment_id);				
		} else {
		  db_perform(TABLE_DATA_SECURITY, $encryption_array, 'insert');			
		}
	  }
	  // check for crm notes
	  $i = 1;
	  while (true) {
	    if (!isset($_POST['im_note_id_'.$i])) break;
		if (!$_POST['im_note_id_'.$i] && ($_POST['crm_act_'.$i] || $_POST['crm_note_'.$i])) {
		  $sql_data_array = array(
		    'contact_id' => $id,
		    'log_date'   => gen_db_date($_POST['crm_date_'.$i]),
		    'action'     => db_prepare_input($_POST['crm_act_' .$i]),
		    'notes'      => db_prepare_input($_POST['crm_note_'.$i]),
		  );
		  db_perform(TABLE_CONTACTS_LOG, $sql_data_array, 'insert');			
		}
		$i++;
	  }	  
	  // check for deletion of crm notes
	  if ($_POST['del_crm_note']) {
		$del_list = substr(trim($_POST['del_crm_note']), 1);
		$db->Execute("delete from " . TABLE_CONTACTS_LOG . " where log_id in (" . $del_list . ")");
	  }
	  // check for deletion of addresses
	  if ($_POST['del_add_id']) {
		$del_list = substr(trim($_POST['del_add_id']), 1);
		$db->Execute("delete from " . TABLE_ADDRESS_BOOK . " where address_id in (" . $del_list . ")");
	  }
	  // check for deletion of payments
	  if ($_POST['del_pmt_id']) {
		$del_list = substr(trim($_POST['del_pmt_id']), 1);
		$db->Execute("delete from " . TABLE_DATA_SECURITY . " where id in (" . $del_list . ")");
	  }
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	}
	$xtra_tab_list = $db->Execute("select id, tab_name, description 
	  from " . TABLE_EXTRA_TABS . " where module_id='contacts' order by sort_order");
	$_POST['special_terms'] = $special_terms; // reload imploded terms to prepare for reload
	$cInfo  = new objectInfo($_POST);
	$action = 'edit';
	$_POST['rowSeq'] = $id;
	// fall through like edit and reload

  case 'edit':
  case 'properties':
    $id = isset($_POST['rowSeq']) ? (int)db_prepare_input($_POST['rowSeq']) : (int)db_prepare_input($_GET['cID']);
  	if (ENABLE_ENCRYPTION) {
	  $sql = "select ref_2, hint from " . TABLE_DATA_SECURITY . " where ref_1 = " . $id;
	  $result = $db->Execute($sql);
	  $payment_data = array();
	  while (!$result->EOF) {
		$payment_data[$result->fields['ref_2']] = $result->fields['hint'];
		$result->MoveNext();
	  }
	}
	$xtra_field_list = $db->Execute("select field_name from " . TABLE_EXTRA_FIELDS . " 
	  where tab_id > 0 and module_id='contacts' order by description");
	$query = '';
	while (!$xtra_field_list->EOF) {
	  $query .= 'c.' . $xtra_field_list->fields['field_name'] . ', ';
	  $xtra_field_list->MoveNext();
	}

	$accounts_query_raw = "select " . $query . "c.id, c.short_name, c.inactive, c.contact_first, 
		c.contact_middle, c.contact_last, c.store_id, c.gl_type_account, 
		c.gov_id_number, c.dept_rep_id, c.account_number, c.special_terms, c.price_sheet, c.tax_id, 
		c.first_date, c.last_update, c.last_date_1, c.last_date_2, 
		a.address_id, a.type, a.primary_name, a.contact, 
		a.address1, a.address2, a.city_town, a.state_province, a.postal_code, 
		a.country_code, a.telephone1, a.telephone2, 
		a.telephone3, a.telephone4, a.email, a.website, a.notes 
	  from " . TABLE_CONTACTS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.id = a.ref_id 
	  where c.id = '" . $id . "' order by a.address_id";
	$contacts = $db->Execute($accounts_query_raw);
	if ($contacts->RecordCount() == 0 && !$error) {  // skip in case of save with an error
	  $messageStack->add(ACT_ERROR_ACCOUNT_NOT_FOUND, 'error');
	  $action = '';
	  break;
	}
	$contact_js = 'var addBook = Array(' . $contacts->RecordCount() . ');' . chr(10);
	while(!$contacts->EOF) {
	  $idx = 0;
	  $add_type = $contacts->fields['type'];
	  // set the hint
	  $hint = (isset($payment_data[$contacts->fields['address_id']])) ? $payment_data[$contacts->fields['address_id']] : '';
	  $contacts->fields[$add_type . '_address'][] = array(
		'address_id'     => $contacts->fields['address_id'],
		'primary_name'   => $contacts->fields['primary_name'],
		'contact'        => $contacts->fields['contact'],
		'address1'       => $contacts->fields['address1'],
		'address2'       => $contacts->fields['address2'],
		'city_town'      => $contacts->fields['city_town'],
		'state_province' => $contacts->fields['state_province'],
		'postal_code'    => $contacts->fields['postal_code'],
		'country_code'   => $contacts->fields['country_code'],
		'telephone1'     => $contacts->fields['telephone1'],
		'telephone2'     => $contacts->fields['telephone2'],
		'telephone3'     => $contacts->fields['telephone3'],
		'telephone4'     => $contacts->fields['telephone4'],
		'email'          => $contacts->fields['email'],
		'website'        => $contacts->fields['website'],
		'notes'          => $contacts->fields['notes'],
		'hint'           => $hint);
	  $contact_js .= contacts_add_address_info($contacts->fields['address_id'], $contacts->fields);
	  $idx++;
	  if (substr($add_type, 1, 1) == 'm') { // pull some special information since it's the main address
	    $edit_text = $contacts->fields['primary_name'] . ' (' . $contacts->fields['short_name'] . ')';
	    $contacts->fields[$add_type . '_notes'] = $contacts->fields['notes'];
	  }
	  $contacts->MoveNext();
	}
	// load payment info
	if ($_SESSION['admin_encrypt']) {
	  $result = $db->Execute("select id, hint, enc_value from " . TABLE_DATA_SECURITY . " where module='contacts' and ref_1=" . $id);
	  $js_pmt_array = "var js_pmt_array = new Array(" . $result->RecordCount() . ");" . chr(10);
	  $cnt = 0;
	  $encrypt = new encryption();
	  while (!$result->EOF) {
	    if (!$values = $encrypt->decrypt($_SESSION['admin_encrypt'], $result->fields['enc_value'])) {
		  $messageStack->add('Encryption error - ' . implode('. ', $encrypt->errors), 'error');
		  $error = true;
	    }
	    $val = explode(':', $values);
	    $js_pmt_array .= 'js_pmt_array[' . $cnt . '] = new pmtRecord("' . $result->fields['id'] . '", "' . $result->fields['hint'] . '", "' . $val[0] . '", "' . $val[1] . '", "' . $val[2] . '", "' . $val[3] . '", "' . $val[4] . '");' . chr(10);
	    $contacts->fields['pmt_values'][] = array(
		  'id'   => $result->fields['id'],
		  'name' => $val[0],
		  'hint' => $result->fields['hint'],
		  'exp'  => $val[2] . '/' . $val[3],
	    );
	    $cnt++;
	    $result->MoveNext();
	  }
	}
	// load crm notes
	if ($type == 'i') {
	  $result = $db->Execute("select log_id, log_date, action, notes from " . TABLE_CONTACTS_LOG . " where contact_id = " . $id);
	  while (!$result->EOF) {
	    $contacts->fields['crm_notes'][] = array(
		  'log_id'   => $result->fields['log_id'],
		  'log_date' => $result->fields['log_date'],
		  'action'   => $result->fields['action'],
		  'notes'    => $result->fields['notes'],
	    );
		$result->MoveNext();
	  }
	}
	$cInfo = new objectInfo($contacts->fields);
	if ($error) { // do this if action was save and error occurred, regen post input
	  foreach ($_POST as $key => $value) $cInfo->$key = $value;
	}
	break;

  case 'delete':
  case 'crm_delete':
	if ($security_level < 4) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	  break;
	}
	$id = (int)db_prepare_input($_POST['rowSeq']);
	$result = $db->Execute("select id from " . TABLE_JOURNAL_MAIN . " 
	  where bill_acct_id = " . $id . " or ship_acct_id = " . $id . " or store_id = " . $id);
	if ($result->RecordCount() == 0) {
	  $db->Execute("delete from " . TABLE_ADDRESS_BOOK  . " where ref_id = " . $id);
	  $db->Execute("delete from " . TABLE_DATA_SECURITY . " where ref_1 = "  . $id);
	  $db->Execute("delete from " . TABLE_CONTACTS      . " where id = "     . $id);
	  gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_DELETE . '-' . constant('ACT_' . strtoupper($type) . '_TYPE_NAME'), gen_get_contact_name($id));
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	}
	$messageStack->add(ACT_ERROR_CANNOT_DELETE,'error');
	break;

  case 'go_first':    $_GET['list'] = 1;     break;
  case 'go_previous': $_GET['list']--;       break;
  case 'go_next':     $_GET['list']++;       break;
  case 'go_last':     $_GET['list'] = 99999; break;
  case 'search':
  case 'search_reset':
  case 'go_page':
  default:
}

/*****************   prepare to display templates  *************************/
for ($i = 1; $i < 13; $i++) {
  $j = ($i < 10) ? '0' . $i : $i;
  $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => $j . ' : ' . strftime('%B',mktime(0,0,0,$i,1,2000)));
}

$today = getdate();
for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
  $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
}

$include_header   = true;
$include_footer   = true;
$include_tabs     = false;
$include_calendar = true;

$tab_list = array();
switch ($action) {
  case 'properties':
	$include_header   = false;
	$include_footer   = false;
    define('PAGE_TITLE', constant('ACT_' . strtoupper($type) . '_PAGE_TITLE_EDIT'));
	// now fall through just like edit
  case 'edit':
  case 'update':
  case 'new':
  	switch ($type) {
	  case 'c': // customers
		$tab_list[] = array('tag'=>'payment',  'order'=>30, 'text'=>TEXT_PAYMENT);
	  case 'v': // vendors
		$tab_list[] = array('tag'=>'addbook',  'order'=>20, 'text'=>TEXT_ADDRESS_BOOK);
		$tab_list[] = array('tag'=>'contacts', 'order'=> 5, 'text'=>TEXT_CONTACTS);
	  case 'e': // employees
		$tab_list[] = array('tag'=>'history',  'order'=>10, 'text'=>TEXT_HISTORY);
	  case 'b': // branches
	  case 'i': // crm contacts
	  case 'j': // jobs/projects
		$tab_list[] = array('tag'=>'notes',    'order'=>40, 'text'=>TEXT_NOTES);
		$tab_list[] = array('tag'=>'general',  'order'=> 1, 'text'=>TEXT_GENERAL);
		break;
	  default:
	}
	// load the tax rates
	$tax_rates        = inv_calculate_tax_drop_down($type);
	// generate a project list array parallel to the drop down for the javascript add line item function
	$js_proj_list = 'var proj_list = new Array(' . count($proj_list) . ');' . chr(10);
	$i = 0;
	$js_actions = 'var actions_list = Array();' . chr(10);
	foreach ($crm_actions as $key => $value) {
	  $js_actions .= 'actions_list[' . $i . '] = new dropDownData("' . $key . '", "' . $value . '");' . chr(10);
	  $i++;
	}
	$xtra_tab_list = $db->Execute("select id, tab_name, description 
	  from " . TABLE_EXTRA_TABS . " where module_id='contacts' order by sort_order");
	$field_list = $db->Execute("select field_name, description, tab_id, params 
	  from " . TABLE_EXTRA_FIELDS . " where module_id='contacts' order by description");
	if ($field_list->RecordCount() < 1) {
	  require_once(DIR_FS_MODULES . 'phreedom/functions/phreedom.php');
	  xtra_field_sync_list('contacts', TABLE_CONTACTS);
	}
    $include_tabs     = true;
    $include_template = 'template_detail.php';
	define('PAGE_TITLE', ($action == 'new') ? $page_title_new : constant('ACT_' . strtoupper($type) . '_PAGE_TITLE_EDIT'));
	break;
  default:
	$heading_array = array('c.short_name' => constant('ACT_' . strtoupper($type) . '_SHORT_NAME'));
    if ($type == 'e') {
		$heading_array['c.contact_last,c.contact_first'] = GEN_EMPLOYEE_NAME;
	} else {
		$heading_array['a.primary_name'] = GEN_PRIMARY_NAME;
	}
	$heading_array['address1']       = GEN_ADDRESS1;
	$heading_array['city_town']      = GEN_CITY_TOWN;
	$heading_array['state_province'] = GEN_STATE_PROVINCE;
	$heading_array['postal_code']    = GEN_POSTAL_CODE;
	$heading_array['telephone1']     = GEN_TELEPHONE1;
	$result      = html_heading_bar($heading_array, $_GET['list_order']);
	$list_header = $result['html_code'];
	$disp_order  = $result['disp_order'];
	// build the list for the page selected
    $criteria[] = "a.type = '" . $type . "m'";
	if ($search_text) {
      $search_fields = array('a.primary_name', 'a.contact', 'a.telephone1', 'a.telephone2', 'a.address1', 
	  	'a.address2', 'a.city_town', 'a.postal_code', 'c.short_name');
	  // hook for inserting new search fields to the query criteria.
	  if (is_array($extra_search_fields)) $search_fields = array_merge($search_fields, $extra_search_fields);
	  $criteria[] = '(' . implode(' like \'%' . $search_text . '%\' or ', $search_fields) . ' like \'%' . $search_text . '%\')';
	}
	if (!$f0) $criteria[] = "(c.inactive = '0' or c.inactive = '')"; // inactive flag

	$search = (sizeof($criteria) > 0) ? (' where ' . implode(' and ', $criteria)) : '';
	$field_list = array('c.id', 'c.inactive', 'c.short_name', 'c.contact_first', 'c.contact_last', 
		'a.telephone1', 'c.first_date', 'c.last_update', 'c.last_date_1', 'c.last_date_2', 
		'a.primary_name', 'a.address1', 'a.city_town', 'a.state_province', 'a.postal_code');
	// hook to add new fields to the query return results
	if (is_array($extra_query_list_fields) > 0) $field_list = array_merge($field_list, $extra_query_list_fields);
    $query_raw = "select " . implode(', ', $field_list)  . " 
		from " . TABLE_CONTACTS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.id = a.ref_id " . $search . " order by $disp_order";
    $query_split  = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
    $query_result = $db->Execute($query_raw);
    $include_template = 'template_main.php'; // include display template (required)
	define('PAGE_TITLE', constant('ACT_' . strtoupper($type) . '_HEADING_TITLE'));
}

?>