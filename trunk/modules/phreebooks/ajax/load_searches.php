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
//  Path: /modules/phreebooks/ajax/load_searches.php
//
/**************   Check user security   *****************************/
$security_level = validate_ajax_user();
/**************  include page specific files    *********************/
require(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php');
/**************   page specific initialization  *************************/
$error       = false;
$debug       = NULL;
$xml         = NULL;
$search_text = db_prepare_input($_GET['guess']);
$type        = db_prepare_input($_GET['type']);
$jID         = db_prepare_input($_GET['jID']);

define('JOURNAL_ID', $jID);
// select the customer and build the contact record
if (isset($search_text) && $search_text <> '') {
  $search_fields = array('a.primary_name', 'a.contact', 'a.telephone1', 'a.telephone2', 'a.address1', 
	'a.address2', 'a.city_town', 'a.postal_code', 'c.short_name');
  $search = ' and (' . implode(' like \'%' . $search_text . '%\' or ', $search_fields) . ' like \'%' . $search_text . '%\')';
} else {
  echo createXmlHeader() . xmlEntry('result', 'fail') . createXmlFooter();
  die;
}
$query_raw = "select c.id from " . TABLE_CONTACTS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.id = a.ref_id 
	where a.type = '" . $type . "m'" . $search . " limit 2";
$result = $db->Execute($query_raw);
if ($result->RecordCount() <> 1) {
  echo createXmlHeader() . xmlEntry('result', 'fail') . createXmlFooter();
  die;
}
$cID = $result->fields['id'];
// select the customer and build the contact record
$contact = $db->Execute("select * from " . TABLE_CONTACTS . " where id = '" . $cID . "'");
$type    = $contact->fields['type'];
define('ACCOUNT_TYPE', $type);
$bill_add = $db->Execute("select * from " . TABLE_ADDRESS_BOOK . " 
  where ref_id = '" . $cID . "' and type in ('" . $type . "m', '" . $type . "b')");
//$debug .= 'contact ID = ' . $cID . ', type = ' . $type . chr(10);
// determine how much the customer owes and remaining credit
$invoices = fill_paid_invoice_array(0, $cID, $type);
$terms    = explode(':', $contact->fields['special_terms']);
$contact->fields['credit_limit'] = $terms[4] ? $terms[4] : ($type == 'v' ? AP_CREDIT_LIMIT_AMOUNT : AR_CREDIT_LIMIT_AMOUNT);
$contact->fields['credit_remaining'] = $contact->fields['credit_limit'] - $invoices['balance'];
// fetch the line items
$item_list = $invoices['invoices'];
if (sizeof($item_list) == 0) {
  echo createXmlHeader() . xmlEntry('result', 'fail') . createXmlFooter(); die;
}
// build the form data
$xml .= xmlEntry('result', 'success');
$xml .= "<BillContact>\n";
foreach ($contact->fields as $key => $value) $xml .= "\t" . xmlEntry($key, $value);
if ($bill_add->fields) while (!$bill_add->EOF) {
  $xml .= "\t<Address>\n";
  foreach ($bill_add->fields as $key => $value) $xml .= "\t\t" . xmlEntry($key, $value);
  $xml .= "\t</Address>\n";
  $bill_add->MoveNext();
}
$xml .= "</BillContact>\n";
if ($debug) $xml .= xmlEntry('debug', $debug);
echo createXmlHeader() . $xml . createXmlFooter();
die;
?>