<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011, 2012 PhreeSoft, LLC       |
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
$tab_list    = array();
if ($_POST['crm_date']) $_POST['crm_date'] = gen_db_date($_POST['crm_date']);
if ($_POST['due_date']) $_POST['due_date'] = gen_db_date($_POST['due_date']);
$search_text = $_POST['search_text'] ? db_input($_POST['search_text']) : db_input($_GET['search_text']);
if (isset($_POST['search_text'])) $_GET['search_text'] = $_POST['search_text']; // save the value for get redirects 
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
require_once(DIR_FS_MODULES . 'phreedom/functions/phreedom.php');
require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
require_once(DIR_FS_WORKING . 'functions/contacts.php');
require_once(DIR_FS_WORKING . 'classes/contacts.php');
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/main/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'new':
  	validate_security($security_level, 2);
	$cInfo = new contacts();
	break;
  case 'save':
	$id             = (int)db_prepare_input($_POST['id']);  // if present, then its an edit
  	$id ? validate_security($security_level, 3) : validate_security($security_level, 2);
	$short_name     = db_prepare_input($_POST['short_name']);
	$inc_auto_id    = false;
	if ($auto_type && !$short_name) {
		$result = $db->Execute("select ".$auto_field." from ".TABLE_CURRENT_STATUS);
		$short_name = $result->fields[$auto_field];
		$inc_auto_id = true;
	}
	// Make some adjustments
	$_POST['special_terms'] = db_prepare_input($_POST['terms']); // TBD will fix when popup terms is redesigned
	// error check
	$address_types = array($type.'m', $type.'s', $type.'b');
	if ($type <> 'i') $address_types[] = 'im'; // add contacts
	foreach ($address_types as $value) {
      if (($value <> 'im' && substr($value, 1, 1) == 'm') || // all main addresses except contacts which is optional
	      ($value == 'im' && $type == 'i') || // contact main address when editing the contact directly
		  ($_POST['address'][$value]['primary_name'] <> '')) { // optional billing, shipping, and contact
		$msg_add_type = GEN_ERRMSG_NO_DATA . constant('ACT_CATEGORY_' . strtoupper(substr($value, 1, 1)) . '_ADDRESS');
		if (false === db_prepare_input($_POST['address'][$value]['primary_name'],   $required = true))                     $error = $messageStack->add($msg_add_type.' - '.GEN_PRIMARY_NAME,  'error');
		if (false === db_prepare_input($_POST['address'][$value]['contact'],        ADDRESS_BOOK_CONTACT_REQUIRED))        $error = $messageStack->add($msg_add_type.' - '.GEN_CONTACT,       'error');
		if (false === db_prepare_input($_POST['address'][$value]['address1'],       ADDRESS_BOOK_ADDRESS1_REQUIRED))       $error = $messageStack->add($msg_add_type.' - '.GEN_ADDRESS1,      'error');
		if (false === db_prepare_input($_POST['address'][$value]['address2'],       ADDRESS_BOOK_ADDRESS2_REQUIRED))       $error = $messageStack->add($msg_add_type.' - '.GEN_ADDRESS2,      'error');
		if (false === db_prepare_input($_POST['address'][$value]['city_town'],      ADDRESS_BOOK_CITY_TOWN_REQUIRED))      $error = $messageStack->add($msg_add_type.' - '.GEN_CITY_TOWN,     'error');
		if (false === db_prepare_input($_POST['address'][$value]['state_province'], ADDRESS_BOOK_STATE_PROVINCE_REQUIRED)) $error = $messageStack->add($msg_add_type.' - '.GEN_STATE_PROVINCE,'error');
		if (false === db_prepare_input($_POST['address'][$value]['postal_code'],    ADDRESS_BOOK_POSTAL_CODE_REQUIRED))    $error = $messageStack->add($msg_add_type.' - '.GEN_POSTAL_CODE,   'error');
		if (false === db_prepare_input($_POST['address'][$value]['telephone1'],     ADDRESS_BOOK_TELEPHONE1_REQUIRED))     $error = $messageStack->add($msg_add_type.' - '.GEN_TELEPHONE1,    'error');
		if (false === db_prepare_input($_POST['address'][$value]['email'],          ADDRESS_BOOK_EMAIL_REQUIRED))          $error = $messageStack->add($msg_add_type.' - '.GEN_EMAIL,         'error');
	  }
	}
	// check for duplicate short_name IDs
	if (!$id) {
	  $result = $db->Execute("select id from ".TABLE_CONTACTS." where short_name = '$short_name' and type = '$type'");
	} else {
	  $result = $db->Execute("select id from ".TABLE_CONTACTS." where short_name = '$short_name' and type = '$type' and id <> $id");
	}
	if ($result->RecordCount() > 0) $error = $messageStack->add(ACT_ERROR_DUPLICATE_ACCOUNT,'error');
	// start saving data
	if (!$error) {
	  $sql_data_array = array(
		'type'            => $type,
		'short_name'      => $short_name,
		'inactive'        => isset($_POST['inactive']) ? '1' : '0',
		'contact_first'   => db_prepare_input($_POST['contact_first']),
		'contact_middle'  => db_prepare_input($_POST['contact_middle']),
		'contact_last'    => db_prepare_input($_POST['contact_last']),
		'store_id'        => db_prepare_input($_POST['store_id']),
		'gl_type_account' => (is_array($_POST['gl_type_account'])) ? implode('', array_keys($_POST['gl_type_account'])) : db_prepare_input($_POST['gl_type_account']),
		'gov_id_number'   => db_prepare_input($_POST['gov_id_number']),
		'dept_rep_id'     => db_prepare_input($_POST['dept_rep_id']),
		'account_number'  => db_prepare_input($_POST['account_number']),
		'special_terms'   => db_prepare_input($_POST['special_terms']),
		'price_sheet'     => db_prepare_input($_POST['price_sheet']),
		'tax_id'          => db_prepare_input($_POST['tax_id']),
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
	  if (!$id) { //create record
		$sql_data_array['first_date'] = 'now()';
		db_perform(TABLE_CONTACTS, $sql_data_array, 'insert');
		$id = db_insert_id();
		// if auto-increment see if the next id is there and increment if so.
		if ($inc_auto_id) { // increment the ID value
			$next_id = string_increment($short_name);
			$db->Execute("update ".TABLE_CURRENT_STATUS." set $auto_field = '$next_id'");
		}
		gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_ADD . '-' . constant('ACT_' . strtoupper($type) . '_TYPE_NAME'), $short_name);
	  } else { // update record
		db_perform(TABLE_CONTACTS, $sql_data_array, 'update', "id = '$id'");
		gen_add_audit_log(TEXT_CONTACTS . '-' . TEXT_UPDATE . '-' . constant('ACT_' . strtoupper($type) . '_TYPE_NAME'), $short_name);
	  }
	  // contact main record
	  if ($type <> 'i' && $_POST['i_short_name']) { // is null
		$i_id         = db_prepare_input($_POST['i_id']);
		$i_short_name = db_prepare_input($_POST['i_short_name']);
		// error check contact
		if (!$i_id) {
		  $result = $db->Execute("select id from ".TABLE_CONTACTS." where short_name = '$i_short_name' and type = 'i'");
		} else { // $action == update
		  $result = $db->Execute("select id from ".TABLE_CONTACTS." where short_name = '$i_short_name' and type = 'i' and id <> $i_id");
		}
		if ($result->RecordCount() > 0) $error = $messageStack->add(ACT_ERROR_DUPLICATE_CONTACT,'error');
		if ($addresses['im']['primary_name'] && !$i_short_name) {
		  $error = $messageStack->add(ACT_I_TYPE_NAME . ': ' . ACT_JS_SHORT_NAME,'error');
		}
	  	if (!$error) {
	  	  $sql_data_array = array(
		    'type'           => 'i',
		    'short_name'     => $i_short_name,
		    'contact_first'  => db_prepare_input($_POST['i_contact_first']),
		    'contact_middle' => db_prepare_input($_POST['i_contact_middle']),
		    'contact_last'   => db_prepare_input($_POST['i_contact_last']),
		    'gov_id_number'  => db_prepare_input($_POST['i_gov_id_number']),
		    'account_number' => db_prepare_input($_POST['i_account_number']),
		    'dept_rep_id'    => $id,
		    'last_update'    => 'now()',
	      );
	      if (!$i_id) { //create record
		    $sql_data_array['first_date'] = 'now()';
		    db_perform(TABLE_CONTACTS, $sql_data_array, 'insert');
		    $i_id = db_insert_id();
	      } else { // update record
		    db_perform(TABLE_CONTACTS, $sql_data_array, 'update', "id = '$i_id'");
	      }
	  	}
	  }
	  // address book fields
	  foreach ($address_types as $value) {
        if (($value <> 'im' && substr($value, 1, 1) == 'm') || // all main addresses except contacts which is optional
	      ($value == 'im' && $type == 'i') || // contact main address when editing the contact directly
		  ($_POST['address'][$value]['primary_name'] <> '')) { // billing, shipping, and contact if primary_name present
	      $sql_data_array = array(
		    'ref_id'         => $id,
		    'type'           => $value,
		    'primary_name'   => $_POST['address'][$value]['primary_name'],
		    'contact'        => $_POST['address'][$value]['contact'],
		    'address1'       => $_POST['address'][$value]['address1'],
		    'address2'       => $_POST['address'][$value]['address2'],
		    'city_town'      => $_POST['address'][$value]['city_town'],
		    'state_province' => $_POST['address'][$value]['state_province'],
		    'postal_code'    => $_POST['address'][$value]['postal_code'],
		    'country_code'   => $_POST['address'][$value]['country_code'],
		    'telephone1'     => $_POST['address'][$value]['telephone1'],
		    'telephone2'     => $_POST['address'][$value]['telephone2'],
		    'telephone3'     => $_POST['address'][$value]['telephone3'],
		    'telephone4'     => $_POST['address'][$value]['telephone4'],
		    'email'          => $_POST['address'][$value]['email'],
		    'website'        => $_POST['address'][$value]['website'],
		    'notes'          => $_POST['address'][$value]['notes'],
		  );
		  if ($type <> 'i' && $value == 'im') $sql_data_array['ref_id'] = $i_id; // re-point contact
		  if (!$_POST['address'][$value]['address_id']) { // then it's a new address
		    db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'insert');
		    $_POST['address'][$value]['address_id'] = db_insert_id();
		  } else { // then update address
		    db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "address_id = '".$_POST['address'][$value]['address_id']."'");
		  }
		  if ($value <> 'im' && substr($value, 1, 1) == 'm' && $encryption_array) $main_add_id = $value['address_id']; // save for payment insert
        }
      }
	  // payment fields
	  if (ENABLE_ENCRYPTION && $_POST['payment_cc_name'] && $_POST['payment_cc_number']) { // save payment info
		$encrypt = new encryption();
		$cc_info = array(
		  'name'    => db_prepare_input($_POST['payment_cc_name']),
		  'number'  => db_prepare_input($_POST['payment_cc_number']),
		  'exp_mon' => db_prepare_input($_POST['payment_exp_month']),
		  'exp_year'=> db_prepare_input($_POST['payment_exp_year']),
		  'cvv2'    => db_prepare_input($_POST['payment_cc_cvv2']),
		);
		if ($enc_value = $encrypt->encrypt_cc($cc_info)) {
		  $payment_array = array(
		    'hint'      => $enc_value['hint'],
		    'module'    => 'contacts',
		    'enc_value' => $enc_value['encoded'],
		    'ref_1'     => $id,
		    'ref_2'     => $main_add_id,
		    'exp_date'  => $enc_value['exp_date'],
		  );
		  db_perform(TABLE_DATA_SECURITY, $payment_array, $_POST['payment_id'] ? 'update' : 'insert', 'id = '.$_POST['payment_id']);				
		} else $error = true;
	  }
	  // Check attachments
	  $result = $db->Execute("select attachments from ".TABLE_CONTACTS." where id = $id");
	  $attachments = $result->fields['attachments'] ? unserialize($result->fields['attachments']) : array();
	  $image_id = 0;
	  while ($image_id < 100) { // up to 100 images
	    if (isset($_POST['rm_attach_'.$image_id])) {
		  @unlink(CONTACTS_DIR_ATTACHMENTS . 'contacts_'.$id.'_'.$image_id.'.zip');
		  unset($attachments[$image_id]);
	    }
	    $image_id++;
	  }
	  if (is_uploaded_file($_FILES['file_name']['tmp_name'])) { // find an image slot to use
	    $image_id = 0;
	    while (true) {
		  if (!file_exists(CONTACTS_DIR_ATTACHMENTS.'contacts_'.$id.'_'.$image_id.'.zip')) break;
		  $image_id++;
	    }
	    saveUploadZip('file_name', CONTACTS_DIR_ATTACHMENTS, 'contacts_'.$id.'_'.$image_id.'.zip');
	    $attachments[$image_id] = $_FILES['file_name']['name'];
	  }
	  $sql_data_array = array('attachments' => sizeof($attachments)>0 ? serialize($attachments) : '');
	  db_perform(TABLE_CONTACTS, $sql_data_array, 'update', 'id = '.$id);
	  // check for crm notes
	  if ($_POST['crm_action'] <> '' || $_POST['crm_note'] <> '') {
		$sql_data_array = array(
		  'contact_id' => $id,
		  'log_date'   => $_POST['crm_date'],
		  'entered_by' => $_POST['crm_rep_id'],
		  'action'     => $_POST['crm_action'],
		  'notes'      => db_prepare_input($_POST['crm_note']),
		);
		db_perform(TABLE_CONTACTS_LOG, $sql_data_array, 'insert');	
	  }
	  gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	}
   	$cInfo  = new contacts($id);
	if ($error) foreach ($_POST as $key => $value) if (isset($cInfo->$key)) $cInfo->$key = $value;
	$action = 'edit';
	break;

  case 'edit':
  case 'properties':
    $id = isset($_POST['rowSeq']) ? (int)db_prepare_input($_POST['rowSeq']) : (int)db_prepare_input($_GET['cID']);
   	$cInfo = new contacts($id);
	break;

  case 'delete':
  case 'crm_delete':
	validate_security($security_level, 4);
	$id         = (int)$_POST['rowSeq'];
	$short_name = gen_get_contact_name($id);
	$contact    = new contacts();
	if ($contact->delete($id)) {
	  gen_add_audit_log(TEXT_CONTACTS.'-'.TEXT_DELETE.'-'.constant('ACT_'.strtoupper($type).'_TYPE_NAME'), $short_name);
	} else {
		$error = $messageStack->add(ACT_ERROR_CANNOT_DELETE,'error');
	}
	break;

  case 'download':
	$cID   = db_prepare_input($_POST['id']);
  	$imgID = db_prepare_input($_POST['rowSeq']);
	$filename = 'contacts_'.$cID.'_'.$imgID.'.zip';
	if (file_exists(CONTACTS_DIR_ATTACHMENTS . $filename)) {
	  require_once(DIR_FS_MODULES . 'phreedom/classes/backup.php');
	  $backup = new backup();
	  $backup->download(CONTACTS_DIR_ATTACHMENTS, $filename, true);
	}
	die;

  case 'dn_attach': // download from list, assume the first document only
	$cID   = db_prepare_input($_POST['rowSeq']);
  	$result = $db->Execute("select attachments from ".TABLE_CONTACTS." where id = $cID");
  	$attachments = unserialize($result->fields['attachments']);
  	foreach ($attachments as $key => $value) {
	  $filename = 'contacts_'.$cID.'_'.$key.'.zip';
	  if (file_exists(CONTACTS_DIR_ATTACHMENTS . $filename)) {
	    require_once(DIR_FS_MODULES . 'phreedom/classes/backup.php');
	    $backup = new backup();
	    $backup->download(CONTACTS_DIR_ATTACHMENTS, $filename, true);
	    die;
	  }
  	}

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
$include_header = true;
$include_footer = true;

switch ($action) {
  case 'properties':
	$include_header   = false;
	$include_footer   = false;
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
	for ($i = 1; $i < 13; $i++) {
	  $j = ($i < 10) ? '0' . $i : $i;
	  $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => $j . ' : ' . strftime('%B',mktime(0,0,0,$i,1,2000)));
	}
	$today = getdate();
	for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
	  $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
	}
	// load the tax rates
	$tax_rates       = inv_calculate_tax_drop_down($type);
	$sales_rep_array = gen_get_rep_ids($type);
	$sale_reps       = array();
	foreach ($sales_rep_array as $value) $sale_reps[$value['id']] = $value['text'];
	$xtra_tab_list = $db->Execute("select id, tab_name, description 
	  from " . TABLE_EXTRA_TABS . " where module_id='contacts' order by sort_order");
	$field_list = $db->Execute("select field_name, description, tab_id, params 
	  from " . TABLE_EXTRA_FIELDS . " where module_id='contacts' order by description");
	if ($field_list->RecordCount() < 1) xtra_field_sync_list('contacts', TABLE_CONTACTS);
    $include_template = 'template_detail.php';
	define('PAGE_TITLE', ($action == 'new') ? $page_title_new : constant('ACT_'.strtoupper($type).'_PAGE_TITLE_EDIT').' - ('.$cInfo->short_name.') '.$cInfo->address[m][0]->primary_name);
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
		'a.telephone1', 'c.attachments', 'c.first_date', 'c.last_update', 'c.last_date_1', 'c.last_date_2', 
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