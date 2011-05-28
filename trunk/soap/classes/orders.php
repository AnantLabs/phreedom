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
//  Path: /soap/classes/orders.php
//

class xml_orders extends parser {
  function xml_orders() {
	$this->response   = array();
	$this->successful = array();
	$this->failed     = array();
  }

	function processXML($rawXML) {
//echo '<pre>' . $rawXML . '</pre>';
	  $rawXML = utf8_decode($rawXML);
	  $rawXML = iconv("UTF-8", "UTF-8//IGNORE", $rawXML); 
//echo '<pre>' . $rawXML . '</pre>';
	  if (!$objXML = xml_to_object($rawXML)) return false;  // parse the submitted string, check for errors
//echo 'parsed string = '; print_r($objXML); echo '<br />';
	  $this->username = $objXML->Request->UserID;
	  $this->password = $objXML->Request->Password;
	  $this->version  = $objXML->Request->Version;
	  $this->function = $objXML->Request->Function;
	  $this->action   = $objXML->Request->Action;
	  $this->validateUser($this->username, $this->password);
	  $this->processOrder($objXML);
	  $extra_response = NULL;
	  if (sizeof($this->successful) > 0) {
		$result_code = '0';
		$result_flag = 'success';
		$extra_response .= xmlEntry('SuccessfulOrders', implode(', ', $this->successful));
	  }
	  if (sizeof($this->failed) > 0) {
		$result_code = '90';
		$result_flag = 'error';
		$extra_response .= xmlEntry('FailedOrders', implode(', ', $this->failed));
	  }
	  $this->responseXML($result_code, implode("<br />", $this->response), $result_flag, $extra_response);
	}

  function processOrder($objXML) {
	global $db;
	// build the tax table to set the tax rates
	switch (strtoupper($this->function)) {
	  case 'SalesInvoice':
		define('JOURNAL_ID',12);
		define('GL_TYPE','sos');
		break;
	  case 'SalesOrder':
	  default:
		define('JOURNAL_ID',10);
		define('GL_TYPE','soo');
	}
	$tax_rates = ord_calculate_tax_drop_down('c');
	// Here we map the received xml array to the pre-defined generic structure (application specific format later)
	if (!is_array($objXML->Request->Order)) $objXML->Request->Order = array($objXML->Request->Order);
	foreach ($objXML->Request->Order as $order) {
//echo 'order = '; print_r($order); echo '<br>';
	  if ($order->ReceivablesGLAccount <> '') { // see if requestor specifies a AR account else use default
	    define('DEF_GL_ACCT', $order->ReceivablesGLAccount);
	  } else {
	    define('DEF_GL_ACCT', AR_DEFAULT_GL_ACCT);
	  }
	  $this->order = array();
	  $this->order['reference']              = $order->Reference;
	  $this->order['store_id']               = $order->StoreID;
	  $this->order['sales_gl_account']       = $order->SalesGLAccount;
	  $this->order['receivables_gl_acct']    = $order->ReceivablesGLAccount;
	  $this->order['order_id']               = $order->OrderID;
	  $this->order['purch_order_id']         = $order->PurchaseOrderID;
	  $this->order['post_date']              = $order->OrderDate;
	  $this->order['order_total']            = $order->OrderTotal;
	  $this->order['tax_total']              = $order->TaxTotal;
	  $this->order['freight_total']          = $order->ShippingTotal;
	  $this->order['freight_carrier']        = $order->ShippingCarrier;
	  $this->order['freight_method']         = $order->ShippingMethod;
	  $this->order['rep_id']                 = $order->SalesRepID;
//	  $this->order['discount_total']         = $order->DiscountTotal;
	  // <Payment>
	  $this->order['payment']['holder_name'] = $order->Payment->CardHolderName;
	  $this->order['payment']['method']      = $order->Payment->Method;
	  $this->order['payment']['type']        = $order->Payment->CardType;
	  $this->order['payment']['card_number'] = $order->Payment->CardNumber;
	  $this->order['payment']['exp_date']    = $order->Payment->ExpirationDate;
	  $this->order['payment']['cvv2']        = $order->Payment->CVV2Number;
// Begin - additional operations added by PhreeSoft for PPS
	  $this->order['payment']['hint']        = $order->Payment->CardHint;
	  $temp                                  = $order->Payment->CardEncodeValue;
	  $this->order['payment']['encval']      = base64_decode(strtr($temp, '-_,', '+/='));
// End - additional operations added by PhreeSoft for PPS
	  // <Customer> and <Billing> and <Shipping>
	  $types = array ('customer', 'billing', 'shipping');
	  foreach ($types as $value) {
	    $entry = ucfirst($value);
	    $this->order[$value]['primary_name']   = $order->$entry->CompanyName;
	    $this->order[$value]['contact']        = $order->$entry->Contact;
	    $this->order[$value]['address1']       = $order->$entry->Address1;
	    $this->order[$value]['address2']       = $order->$entry->Address2;
	    $this->order[$value]['city_town']      = $order->$entry->CityTown;
	    $this->order[$value]['state_province'] = $order->$entry->StateProvince;
	    $this->order[$value]['postal_code']    = $order->$entry->PostalCode;
	    $this->order[$value]['country_code']   = $order->$entry->CountryCode;
	    $this->order[$value]['telephone']      = $order->$entry->Telephone;
	    $this->order[$value]['email']          = $order->$entry->Email;
	    if ($value == 'customer') { // additional information for the customer record
		  $this->order[$value]['customer_id']  = $order->$entry->CustomerID;
	    }
	  }
	  // if billing or shipping is blank, use customer address
	  if ($this->order['billing']['primary_name'] == '' && $this->order['billing']['contact'] == '') {
	    $this->order['billing'] = $this->order['customer'];
	  }
	  if ($this->order['shipping']['primary_name'] == '' && $this->order['shipping']['contact'] == '') {
	    $this->order['shipping'] = $this->order['customer'];
	  }
	  // <LineItems>
	  $this->order['items'] = array();
	  if (!is_array($order->Item)) $order->Item = array($order->Item);
	  foreach ($order->Item as $entry) {
		$item = array();
		$sku                 = $entry->ItemID;
		// try to match sku and get the sales gl account
		$result = $db->Execute("select account_sales_income from " . TABLE_INVENTORY . " where sku = '" . $sku . "'");
		if ($result->RecordCount() > 0) {
		  $item['sku']       = $sku;
		  $item['gl_acct']   = $result->fields['account_sales_income'];
		} else {
		  $result = $db->Execute("select sku, account_sales_income from " . TABLE_INVENTORY . " where description_short = '" . $sku . "'");
		  $item['sku']       = $result->fields['sku'];
		  $item['gl_acct']   = $result->fields['account_sales_income'];
		}
		$item['description'] = $entry->Description;
		$item['quantity']    = $entry->Quantity;
		$item['unit_price']  = $entry->UnitPrice;
		$item['tax_percent'] = $entry->SalesTaxPercent;
//		$item['sales_tax']   = $entry->SalesTax; // sales tax will be calculated
		$item['taxable']     = $this->guess_tax_id($tax_rates, $item['tax_percent']);
		$item['total_price'] = $entry->TotalPrice;
		$this->order['items'][] = $item;
	  }
	  $this->buildJournalEntry();
	}
	return true;
  }

// The remaining functions are specific to PhreeBooks. They need to be modified for the specific application.
// It also needs to check for errors, i.e. missing information, bad data, etc. 
  function buildJournalEntry() {
	global $db, $messageStack, $currencies;
	// set some preliminary information
	$account_type = 'c';
	$psOrd = new orders();
	// make the received string look like a form submission then post as usual
	$psOrd->account_type        = $account_type;
	$psOrd->id                  = ''; // should be null unless opening an existing purchase/receive
	$psOrd->journal_id          = JOURNAL_ID;
	$psOrd->post_date           = $this->order['post_date']; // date format should already be YYYY-MM-DD
	$psOrd->terminal_date       = $this->order['post_date']; // make same as order date for now
	$psOrd->period              = gen_calculate_period($psOrd->post_date);
	$psOrd->store_id            = $this->get_account_id($this->order['store_id'], 'b');
	$psOrd->admin_id            = $this->get_user_id($this->username);
	$psOrd->description         = SOAP_XML_SUBMITTED_SO;
	$psOrd->gl_acct_id          = DEF_GL_ACCT;
	$psOrd->freight             = $currencies->clean_value(db_prepare_input($this->order['freight_total']), DEFAULT_CURRENCY);
	$psOrd->discount            = $currencies->clean_value(db_prepare_input($this->order['discount_total']), DEFAULT_CURRENCY);
	$psOrd->sales_tax           = db_prepare_input($this->order['tax_total']);
	$psOrd->total_amount        = db_prepare_input($this->order['order_total']);
	// The order ID should be set by the submitter
	$psOrd->purchase_invoice_id = db_prepare_input($this->order['order_id']);
	$psOrd->purch_order_id      = db_prepare_input($this->order['purch_order_id']);
	$psOrd->shipper_code        = db_prepare_input($this->order['freight_carrier']);
// BOF - Added by PhreeSoft for Portable Power Systems to map the shipping codes to PhreeBooks Codes
	$psOrd->shipper_code        = substr($psOrd->shipper_code, 0, 16);
	switch ($psOrd->shipper_code) {
	  default: 
	  case 'FedEx (Ground):':  $psOrd->shipper_code = 'fedex_v7:GND';    break;
	  case 'FedEx (Ground LT': $psOrd->shipper_code = 'fedex_v7:GndFrt'; break;
	  case 'FedEx (Home Deli': $psOrd->shipper_code = 'fedex_v7:GDR';    break;
	  case 'FedEx (Express S': $psOrd->shipper_code = 'fedex_v7:3Dpm';   break;
	  case 'FedEx (Express 2': $psOrd->shipper_code = 'fedex_v7:2Dpm';   break;
	  case 'FedEx (Standard ': $psOrd->shipper_code = 'fedex_v7:1Dpm';   break;
	  case 'FedEx (Priority ': $psOrd->shipper_code = 'fedex_v7:1Dam';   break;
	  case 'UPS (Ground):':
	  case 'UPS (Ground Resi': $psOrd->shipper_code = 'ups:GND';         break;
	  case 'UPS (3 Day Selec': $psOrd->shipper_code = 'ups:3Dpm';        break;
	  case 'UPS (2 Day Air):': $psOrd->shipper_code = 'ups:2Dpm';        break;
	  case 'UPS (Next Day PM': $psOrd->shipper_code = 'ups:1Dpm';        break;
	  case 'UPS (Next Day):':  $psOrd->shipper_code = 'ups:1Dam';        break;
	  case 'FREE SHIPPING! (': $psOrd->shipper_code = 'usps:3Dpm';       break;
	  case 'Flat (Best Way):': $psOrd->shipper_code = 'usps:2Dpm';       break;
	}
// EOF - Added by PhreeSoft for PPS

	/* Values below are not used at this time
	$psOrd->sales_tax_auths
	$psOrd->terms
	$psOrd->drop_ship = 0;
	$psOrd->waiting = 0;
	$psOrd->closed = 0;
	$psOrd->subtotal
	*/
	$psOrd->bill_add_update = 1; // force an address book update
	// see if the customer record exists
	$psOrd->short_name          = db_prepare_input($this->order['customer']['customer_id']);
	$psOrd->ship_short_name     = $psOrd->short_name;
	if (!$result = $this->checkForCustomerExists($psOrd)) return;
	$psOrd->ship_add_update     = $result['ship_add_update'];
	$psOrd->bill_acct_id        = $result['bill_acct_id'];
	$psOrd->bill_address_id     = $result['bill_address_id'];
	$psOrd->ship_acct_id        = $result['ship_acct_id'];
	$psOrd->ship_address_id     = $result['ship_address_id'];
	// Phreebooks requires a primary name or the order is not valid, use company name if exists, else contact name
	if ($this->order['billing']['primary_name'] == '') {
	  $psOrd->bill_primary_name = $this->order['billing']['contact'];
	  $psOrd->bill_contact      = '';
	} else {
	  $psOrd->bill_primary_name = $this->order['billing']['primary_name'];
	  $psOrd->bill_contact      = $this->order['billing']['contact'];
	}
	$psOrd->bill_address1       = $this->order['billing']['address1'];
	$psOrd->bill_address2       = $this->order['billing']['address2'];
	$psOrd->bill_city_town      = $this->order['billing']['city_town'];
	$psOrd->bill_state_province = $this->order['billing']['state_province'];
	$psOrd->bill_postal_code    = $this->order['billing']['postal_code'];
	$psOrd->bill_country_code   = gen_get_country_iso_3_from_2($this->order['billing']['country_code']);
	$psOrd->bill_telephone1     = $this->order['customer']['telephone'];
	$psOrd->bill_email          = $this->order['customer']['email'];
	if ($this->order['shipping']['primary_name'] == '') {
	  $psOrd->ship_primary_name = $this->order['shipping']['contact'];
	  $psOrd->ship_contact      = '';
	} else {
	  $psOrd->ship_primary_name = $this->order['shipping']['primary_name'];
	  $psOrd->ship_contact      = $this->order['shipping']['contact'];
	}
	$psOrd->ship_address1       = $this->order['shipping']['address1'];
	$psOrd->ship_address2       = $this->order['shipping']['address2'];
	$psOrd->ship_city_town      = $this->order['shipping']['city_town'];
	$psOrd->ship_state_province = $this->order['shipping']['state_province'];
	$psOrd->ship_postal_code    = $this->order['shipping']['postal_code'];
	$psOrd->ship_country_code   = gen_get_country_iso_3_from_2($this->order['shipping']['country_code']);
	$psOrd->ship_telephone1     = $this->order['customer']['telephone'];
	$psOrd->ship_email          = $this->order['customer']['email'];
	// check for truncation of addresses
	if (strlen($psOrd->bill_primary_name) > 32 || strlen($psOrd->bill_address1) > 32 || strlen($psOrd->ship_primary_name) > 32 || strlen($psOrd->ship_address1) > 32) {
	  $messageStack->add('Either the Primary Name or Address has been truncated to fit in the PhreeBooks database field sizes. Please check source information.', 'caution');
	}
	// load the item rows
	switch (JOURNAL_ID) {
	  case 12: $index = 'pstd'; break;
	  case 10: 
	  default: $index = 'qty';  break;
	}
	for ($i = 0; $i < count($this->order['items']); $i++) {
	  $psOrd->item_rows[] = array(
		'gl_type' => GL_TYPE,
		$index    => db_prepare_input($this->order['items'][$i]['quantity']),
		'sku'     => db_prepare_input($this->order['items'][$i]['sku']),
		'desc'    => db_prepare_input($this->order['items'][$i]['description']),
		'price'   => db_prepare_input($this->order['items'][$i]['unit_price']),
		'acct'    => db_prepare_input($this->order['items'][$i]['gl_acct']),
		'tax'     => db_prepare_input($this->order['items'][$i]['taxable']),
		'total'   => db_prepare_input($this->order['items'][$i]['total_price']),
	  );
	}
	// error check input
	$missing_fields = array();
	if (!$psOrd->short_name)                                                  $missing_fields[] = ACT_SHORT_NAME;
	if (!$psOrd->post_date)                                                   $missing_fields[] = TEXT_POST_DATE;
	if (!$psOrd->period)                                                      $missing_fields[] = TEXT_PERIOD;
	if (!$psOrd->bill_primary_name)                                           $missing_fields[] = GEN_PRIMARY_NAME;
	if (!$psOrd->bill_country_code)                                           $missing_fields[] = GEN_COUNTRY_CODE;
	if (!$psOrd->ship_primary_name)                                           $missing_fields[] = GEN_PRIMARY_NAME;
	if (!$psOrd->ship_country_code)                                           $missing_fields[] = GEN_COUNTRY_CODE;
	if (ADDRESS_BOOK_CONTACT_REQUIRED        && !$psOrd->bill_contact)        $missing_fields[] = GEN_CONTACT;
	if (ADDRESS_BOOK_ADDRESS1_REQUIRED       && !$psOrd->bill_address1)       $missing_fields[] = GEN_ADDRESS1;
	if (ADDRESS_BOOK_ADDRESS2_REQUIRED       && !$psOrd->bill_address2)       $missing_fields[] = GEN_ADDRESS2;
	if (ADDRESS_BOOK_CITY_TOWN_REQUIRED      && !$psOrd->bill_city_town)      $missing_fields[] = GEN_CITY_TOWN;
	if (ADDRESS_BOOK_STATE_PROVINCE_REQUIRED && !$psOrd->bill_state_province) $missing_fields[] = GEN_STATE_PROVINCE;
	if (ADDRESS_BOOK_POSTAL_CODE_REQUIRED    && !$psOrd->bill_postal_code)    $missing_fields[] = GEN_POSTAL_CODE;
	if (ADDRESS_BOOK_CONTACT_REQUIRED        && !$psOrd->ship_contact)        $missing_fields[] = GEN_CONTACT;
	if (ADDRESS_BOOK_ADDRESS1_REQUIRED       && !$psOrd->ship_address1)       $missing_fields[] = GEN_ADDRESS1;
	if (ADDRESS_BOOK_ADDRESS2_REQUIRED       && !$psOrd->ship_address2)       $missing_fields[] = GEN_ADDRESS2;
	if (ADDRESS_BOOK_CITY_TOWN_REQUIRED      && !$psOrd->ship_city_town)      $missing_fields[] = GEN_CITY_TOWN;
	if (ADDRESS_BOOK_STATE_PROVINCE_REQUIRED && !$psOrd->ship_state_province) $missing_fields[] = GEN_STATE_PROVINCE;
	if (ADDRESS_BOOK_POSTAL_CODE_REQUIRED    && !$psOrd->ship_postal_code)    $missing_fields[] = GEN_POSTAL_CODE;
	if (sizeof($missing_fields) > 0) {
	  $this->failed[]   = $this->order['reference'];
	  $this->response[] = sprintf(SOAP_MISSING_FIELDS, $this->order['reference'], implode(', ', $missing_fields));
	  return;
	}
	// post the sales order
//echo 'ready to post =><br />'; echo 'psOrd object = '; print_r($psOrd); echo '<br />';
	$post_success = $psOrd->post_ordr($action);
	if (!$post_success) { // extract the error message from the messageStack and return with error
// echo 'failed a post need to rollback here.<br>';
	  $db->transRollback();
	  $this->failed[] = $this->order['reference'];
	  $text = strip_tags($messageStack->output());
	  $this->response[] = preg_replace('/&nbsp;/', '', $text); // the &nbsp; messes up the response XML
	  return;
	}

// Begin - additional operations added by PhreeSoft for PPS
	if ($this->order['payment']['encval']) {
	  $sql_array = array(
		'module'    => 'contacts',
		'ref_1'     => $psOrd->bill_acct_id,
		'ref_2'     => $psOrd->bill_address_id,
		'hint'      => $this->order['payment']['hint'],
		'enc_value' => $this->order['payment']['encval'],
	  );
	  $result = $db->Execute("select id from " . TABLE_DATA_SECURITY . " 
		where module = 'contacts' 
		and ref_1 = '" . $psOrd->bill_acct_id . "' 
		and ref_2 = '" . $psOrd->bill_address_id . "' 
		and hint  = '" . $this->order['payment']['hint'] . "'");
	  if ($result->RecordCount() > 0) {
		db_perform(TABLE_DATA_SECURITY, $sql_array, 'update', 'id = ' . $result->fields['id']);
	  } else {
		db_perform(TABLE_DATA_SECURITY, $sql_array, 'insert');
	  }
	}
// End - additional operations added by PhreeSoft for PPS

	gen_add_audit_log(constant('AUDIT_LOG_SOAP_' . JOURNAL_ID . '_ADDED'), $psOrd->purchase_invoice_id, $psOrd->total_amount);
	$this->successful[] = $this->order['reference'];
	return;
}

  function checkForCustomerExists($psOrd) {
	global $db;
	$output = array();
	$result = $db->Execute("select id from " . TABLE_CONTACTS . " 
		where type = 'c' and short_name = '" . $psOrd->short_name . "'");
	if ($result->RecordCount() == 0) { // create new record
	  $output['bill_acct_id']    = '';
	  $output['ship_acct_id']    = '';
	  $output['bill_address_id'] = '';
	} else {
	  $output['bill_acct_id'] = $result->fields['id'];
	  $output['ship_acct_id'] = $output['bill_acct_id']; // no drop ships allowed
	  // find main address to update as billing address
	  $result = $db->Execute("select address_id from " . TABLE_ADDRESS_BOOK . " 
		where type = 'cm' and ref_id = " . $output['bill_acct_id']);
	  if ($result->RecordCount() == 0) {
	    $this->failed[] = $this->order['reference'];
	    $this->response[] = SOAP_ACCOUNT_PROBLEM;
	    return false;
	  }
	  $output['bill_address_id'] = $result->fields['address_id'];
	}
	// check to see if billing and shipping are different, if so set ship update flag
	// for now look at the primary name or address1 to be different, can be expanded to differentiate further if necessary
	if (($psOrd->bill_primary_name <> $psOrd->ship_primary_name) || ($psOrd->bill_address1 <> $psOrd->ship_address1)) {
	  $result = $db->Execute("select address_id from " . TABLE_ADDRESS_BOOK . " 
		where primary_name = '" . $psOrd->ship_primary_name . "' and 
			address1 = '" . $psOrd->ship_address1 . "' and 
			type = 'cs' and ref_id = " . $output['bill_acct_id']);
	  $output['ship_add_update'] = 1;
	  $output['ship_address_id'] =  ($result->RecordCount() == 0) ? '' : $result->fields['address_id'];
	} else {
	  $output['ship_add_update'] = 0;
	  $output['ship_address_id'] = $output['bill_address_id'];
	}
	return $output;
  }

  function guess_tax_id($rate_array, $rate) {
	foreach ($rate_array as $value) if ($value['rate'] == $rate) return $value['id'];
	return 0; // no tax since no rate match
  }

}
?>