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
//  Path: /modules/contacts/classes/contacts.php
//
class contacts {	
	function contacts($id = 0, $type = 'c') {
  	global $db, $messageStack;
  	// fill in defaults
  	$this->crm_rep_id        = $_SESSION['admin_id'];
  	$this->crm_date          = date('Y-m-d');
  	$this->crm_action        = '';
  	$this->crm_note          = '';
  	$this->payment_cc_name   = '';
  	$this->payment_cc_number = '';
  	$this->payment_exp_month = '';
  	$this->payment_exp_year  = '';
  	$this->payment_cc_cvv2   = '';
  	$this->special_terms     = '0';
  	 
	if (!$id) return; // new record
	// Load contact info, including custom fields
	$result = $db->Execute("select * from ".TABLE_CONTACTS." where id = $id");
	foreach ($result->fields as $key => $value) $this->$key = $value;
	// expand attachments
	$this->attachments = $result->fields['attachments'] ? unserialize($result->fields['attachments']) : array();
	// Load the address book
	$result = $db->Execute("select * from ".TABLE_ADDRESS_BOOK." where ref_id = $id order by primary_name");
	$this->address = array();
	while (!$result->EOF) {
	  $type = substr($result->fields['type'], 1);
	  $this->address_book[$type][] = new objectInfo($result->fields);
	  if ($type == 'm') { // prefill main address
	  	foreach ($result->fields as $key => $value) $this->address[$result->fields['type']][$key] = $value;
	  }
	  $result->MoveNext();
	}
	// load payment info
	if ($_SESSION['admin_encrypt'] && ENABLE_ENCRYPTION) {
	  $result = $db->Execute("select id, hint, enc_value from ".TABLE_DATA_SECURITY." where module='contacts' and ref_1=$id");
	  $encrypt = new encryption();
	  while (!$result->EOF) {
	    if (!$values = $encrypt->decrypt($_SESSION['admin_encrypt'], $result->fields['enc_value'])) {
		  $error = $messageStack->add('Encryption error - ' . implode('. ', $encrypt->errors), 'error');
	    }
	    $val = explode(':', $values);
	    $this->payment_data[] = array(
		  'id'   => $result->fields['id'],
		  'name' => $val[0],
		  'hint' => $result->fields['hint'],
		  'exp'  => $val[2] . '/' . $val[3],
	    );
	    $result->MoveNext();
	  }
	}
	// load contacts info
	$result = $db->Execute("select * from " . TABLE_CONTACTS . " where dept_rep_id = $id");
	$this->contacts = array();
	while (!$result->EOF) {
	  $cObj = new objectInfo();
	  foreach ($result->fields as $key => $value) $cObj->$key = $value;
	  $addRec = $db->Execute("select * from " . TABLE_ADDRESS_BOOK . " where type = 'im' and ref_id = ".$result->fields['id']);
	  $cObj->address['m'][] = new objectInfo($addRec->fields);
	  $this->contacts[] = $cObj; //unserialize(serialize($cObj));
	  $result->MoveNext();
	}
	// load crm notes
	$result = $db->Execute("select * from ".TABLE_CONTACTS_LOG." where contact_id = $id order by log_date desc");
	while (!$result->EOF) {
	  $this->crm_log[] = new objectInfo($result->fields);
	  $result->MoveNext();
	}
  }

  function delete($id) {
  	global $db;
  	if (!$id) return false;
	// error check, no delete if a journal entry exists
	$result = $db->Execute("select id from ".TABLE_JOURNAL_MAIN." where bill_acct_id = $id or ship_acct_id = $id or store_id = $id limit 1");
	if ($result->RecordCount() == 0) {
	  $db->Execute("delete from ".TABLE_ADDRESS_BOOK ." where ref_id = $id");
	  $db->Execute("delete from ".TABLE_DATA_SECURITY." where ref_1 = $id");
	  $db->Execute("delete from ".TABLE_CONTACTS     ." where id = $id");
	  $db->Execute("delete from ".TABLE_CONTACTS_LOG ." where contact_id = $id");
	  foreach (glob(CONTACTS_DIR_ATTACHMENTS.'contacts_'.$id.'_*.zip') as $filename) unlink($filename); // remove attachments
	}
  	return true;
  }

  function load_open_orders($acct_id, $journal_id, $only_open = true, $limit = 0) {
  	global $db;
  	if (!$acct_id) return array();
  	$sql  = "select id, journal_id, closed, closed_date, post_date, total_amount, purchase_invoice_id, purch_order_id from ".TABLE_JOURNAL_MAIN." where";
  	$sql .= ($only_open) ? " closed = '0' and " : "";
  	$sql .= " journal_id in (" . $journal_id . ") and bill_acct_id = " . $acct_id . ' order by post_date DESC';
  	$sql .= ($limit) ? " limit " . $limit : "";
  	$result = $db->Execute($sql);
  	if ($result->RecordCount() == 0) return array();	// no open orders
  	$output = array(array('id' => '', 'text' => TEXT_NEW));
  	while (!$result->EOF) {
  	  $output[] = array(
  	    'id'                 => $result->fields['id'],
  	    'journal_id'         => $result->fields['journal_id'],
  	    'text'               => $result->fields['purchase_invoice_id'],
  		'post_date'          => $result->fields['post_date'],
  		'closed'             => $result->fields['closed'],
  		'closed_date'        => $result->fields['closed_date'],
  		'total_amount'       => in_array($result->fields['journal_id'], array(7,13)) ? -$result->fields['total_amount'] : $result->fields['total_amount'],
  		'purchase_invoice_id'=> $result->fields['purchase_invoice_id'],
  		'purch_order_id'     => $result->fields['purch_order_id'],
  	  );
  	  $result->MoveNext();
  	}
  	return $output;
  }

}
?>