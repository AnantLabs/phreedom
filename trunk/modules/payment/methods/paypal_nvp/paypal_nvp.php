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
//  Path: /modules/payment/methods/paypal_nvp/paypal_nvp.php
//
// PayPal Payment Pro Module
// Revision history
// 2011-07-01 - Added version number for revision control
define('MODULE_PAYMENT_PAYPAL_NVP_VERSION','3.2');
gen_pull_language('payment');

class paypal_nvp {
  function paypal_nvp() {
    global $order;
    $this->code = 'paypal_nvp';
	$this->enable_encryption = 1; // set to field position of credit card to create hint, false to turn off encryption
    if ((int)MODULE_PAYMENT_PAYPAL_NVP_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_PAYPAL_NVP_ORDER_STATUS_ID;
    }
	// save the information
	// Card numbers are not saved, instead keep the first and last four digits and fill middle with *'s
	$card_number = trim($_POST['paypal_nvp_field_1']);
	$card_number = substr($card_number, 0, 4) . '********' . substr($card_number, -4);
	$this->payment_fields = implode(':', array($_POST['paypal_nvp_field_0'], $card_number, $_POST['paypal_nvp_field_2'], $_POST['paypal_nvp_field_3'], $_POST['paypal_nvp_field_4']));
	$this->avs_codes = array(
		'A' => 'Address matches - Postal Code does not match.',
		'B' => 'Street address match, Postal code in wrong format. (International issuer)',
		'C' => 'Street address and postal code in wrong formats.',
		'D' => 'Street address and postal code match. (international issuer)',
		'E' => 'AVS Error.',
		'G' => 'Service not supported by non-US issuer.',
		'I' => 'Address information not verified by international issuer.',
		'M' => 'Street address and Postal code match. (international issuer)',
		'N' => 'No match on address (street) or postal code.',
		'O' => 'No response sent.',
		'P' => 'Postal code matches, street address not verified due to incompatible formats.',
		'R' => 'Retry, system unavailable or timed out.',
		'S' => 'Service not supported by issuer.',
		'U' => 'Address information is unavailable.',
		'W' => '9 digit postal code matches, address (street) does not match.',
		'X' => 'Exact AVS match.',
		'Y' => 'Address (street) and 5 digit postal code match.',
		'Z' => '5 digit postal code matches, address (street) does not match.'
	);
	$this->cvv_codes = array(
		'M' => 'CVV2 match',
		'N' => 'CVV2 No match',
		'P' => 'Not Processed',
		'S' => 'Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated that the CVV2 data is not present on the card.',
		'U' => 'Issuer has not certified for CVV2 or issuer has not provided Visa with the CVV2 encryption keys.'
	);
  }

  function keys() {
    return array(
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_USER_ID',           'default' => ''),
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_PW',                'default' => ''),
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_SIG',               'default' => ''),
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_TESTMODE',          'default' => 'live'),
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_AUTHORIZATION_TYPE','default' => 'Sale'),
	  array('key' => 'MODULE_PAYMENT_PAYPAL_NVP_SORT_ORDER',        'default' => '3'),
	);
  }

  function configure($key) {
    switch ($key) {
	  case 'MODULE_PAYMENT_PAYPAL_NVP_TESTMODE':
	    $temp = array(
		  array('id' => 'sandbox', 'text' => TEXT_TEST),
		  array('id' => 'live',    'text' => TEXT_PRODUCTION),
	    );
	    $html .= html_pull_down_menu(strtolower($key), $temp, constant($key));
	    break;
	  case 'MODULE_PAYMENT_PAYPAL_NVP_AUTHORIZATION_TYPE':
	    $temp = array(
		  array('id' => 'Authorization','text' => TEXT_AUTHORIZE),
		  array('id' => 'Sale',         'text' => TEXT_CAPTURE),
	    );
	    $html .= html_pull_down_menu(strtolower($key), $temp, constant($key));
	    break;
	  default:
	    $html .= html_input_field(strtolower($key), constant($key), '');
    }
    return $html;
  }

  function update() {
    foreach ($this->keys() as $key) {
      $field = strtolower($key['key']);
      if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
    }
  }

  function javascript_validation() {
    $js = 
	'  if (payment_method == "' . $this->code . '") {' . "\n" .
    '    var cc_owner = document.getElementById("paypal_nvp_field_0").value +" "+document.getElementById("paypal_nvp_field_5").value;' . "\n" .
    '    var cc_number = document.getElementById("paypal_nvp_field_1").value;' . "\n" . 
    '    var cc_cvv = document.getElementById("paypal_nvp_field_4").value;' . "\n" . 
    '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
    '      error_message = error_message + "' . MODULE_PAYMENT_CC_TEXT_JS_CC_OWNER . '";' . "\n" .
    '      error = 1;' . "\n" .
    '    }' . "\n" .
    '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
    '      error_message = error_message + "' . MODULE_PAYMENT_CC_TEXT_JS_CC_NUMBER . '";' . "\n" .
    '      error = 1;' . "\n" .
    '    }' . "\n" . 
    '    if (cc_cvv == "" || cc_cvv.length < "3" || cc_cvv.length > "4") {' . "\n".
    '      error_message = error_message + "' . MODULE_PAYMENT_CC_TEXT_JS_CC_CVV . '";' . "\n" .
    '      error = 1;' . "\n" .
    '    }' . "\n" . 
    '  }' . "\n";
    return $js;
  }

  function selection() {
    global $order;

    for ($i=1; $i<13; $i++) {
      $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
    }

    $today = getdate();
    for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
      $expires_year[] = array('id' => strftime('%Y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
    }
	$selection = array(
	   'id'     => $this->code,
	   'page' => MODULE_PAYMENT_CC_TEXT_CATALOG_TITLE,
	   'fields' => array(
			array(
				'title' => MODULE_PAYMENT_PAYPAL_NVP_TEXT_CREDIT_CARD_OWNER,
				'field' => html_input_field('paypal_nvp_field_0', $order->paypal_nvp_field_0, 'size="12" maxlength="25"') . '&nbsp;' . html_input_field('paypal_nvp_field_5', $order->paypal_nvp_field_5, 'size="12" maxlength="25"'),
			),
			array(
				'title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER,
				'field' => html_input_field('paypal_nvp_field_1', $order->paypal_nvp_field_1),
			),
			array(
				'title' => MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES,
				'field' => html_pull_down_menu('paypal_nvp_field_2', $expires_month, $order->paypal_nvp_field_2) . '&nbsp;' . html_pull_down_menu('paypal_nvp_field_3', $expires_year, $order->paypal_nvp_field_3),
			),
			array(
				'title' => MODULE_PAYMENT_CC_TEXT_CVV,
				'field' => html_input_field('paypal_nvp_field_4', $order->paypal_nvp_field_4, 'size="4" maxlength="4"'),
			),
		),
	);
    return $selection;
  }

  function pre_confirmation_check() {
    global $_POST, $messageStack;

	// if the card number has the blanked out middle number fields, it has been processed, show message that 
	// the charges were not processed through the merchant gateway and continue posting payment.
	if (strpos($_POST['paypal_nvp_field_1'],'*') !== false) {
    	$messageStack->add(MODULE_PAYMENT_CC_NO_DUPS, 'caution');
		return false;
	}

    include_once(DIR_FS_MODULES . 'payment/classes/cc_validation.php');
    $cc_validation = new cc_validation();
    $result = $cc_validation->validate($_POST['paypal_nvp_field_1'], $_POST['paypal_nvp_field_2'], substr($_POST['paypal_nvp_field_3'], 2), $_POST['paypal_nvp_field_4']);
    $error = '';
    switch ($result) {
      case -1:
      $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
      break;
      case -2:
      case -3:
      case -4:
      $error = TEXT_CCVAL_ERROR_INVALID_DATE;
      break;
      case false:
      $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
      break;
    }

    if ( ($result == false) || ($result < 1) ) {
      $messageStack->add($error . '<!-- ['.$this->code.'] -->', 'error');
      return true;
    }

    $this->cc_card_type    = $cc_validation->cc_type;
    $this->cc_card_number  = $cc_validation->cc_number;
    $this->cc_cvv2         = $_POST['paypal_nvp_field_4'];
    $this->cc_expiry_month = $cc_validation->cc_expiry_month;
    $this->cc_expiry_year  = $cc_validation->cc_expiry_year;
	return false;
  }

  function before_process() {
    global $order, $db, $messageStack;

	// if the card number has the blanked out middle number fields, it has been processed, the message that 
	// the charges were not processed were set in pre_confirmation_check, just return to continue without processing.
	if (strpos($_POST['paypal_nvp_field_1'], '*') !== false) return false;

	$this->cc_card_owner = $_POST['paypal_nvp_field_0'] . ' ' . $_POST['paypal_nvp_field_5'];
	switch (substr($_POST['paypal_nvp_field_1'], 0, 1)) {
	  case '3': $card_type = 'Amex';       break;
	  case '4': $card_type = 'Visa';       break;
	  case '5': $card_type = 'MasterCard'; break;
	  case '6': $card_type = 'Discover';   break;
	}
	// Set request-specific fields.
    $submit_data = array(
		'PAYMENTACTION'  => MODULE_PAYMENT_PAYPAL_NVP_AUTHORIZATION_TYPE,
		'AMT'            => $order->total_amount,
		'SHIPPINGAMT'    => $order->freight,
		'TAXAMT'         => $order->sales_tax ? $order->sales_tax : 0,
		'DESC'           => $order->description,
		'INVNUM'         => $order->purchase_invoice_id,
		'CREDITCARDTYPE' => $card_type,
		'ACCT'           => preg_replace('/ /', '', $_POST['paypal_nvp_field_1']),
		'EXPDATE'        => $_POST['paypal_nvp_field_2'] . $_POST['paypal_nvp_field_3'],
		'CVV2'           => $_POST['paypal_nvp_field_4'] ? $_POST['paypal_nvp_field_4'] : '',
		'PAYERID'        => $order->bill_short_name,
		'FIRSTNAME'      => $_POST['paypal_nvp_field_0'],
		'LASTNAME'       => $_POST['paypal_nvp_field_5'],
		'STREET'         => str_replace('&', '-', substr($order->bill_address1, 0, 20)),
		'STREET2'        => str_replace('&', '-', substr($order->bill_address2, 0, 20)),
		'CITY'           => $order->bill_city_town,
		'STATE'          => $order->bill_state_province,
		'ZIP'            => preg_replace("/[^A-Za-z0-9]/", "", $order->bill_postal_code),
		'COUNTRYCODE'    => gen_get_country_iso_2_from_3($order->bill_country_code),
		'EMAIL'          => $order->bill_email,
		'PHONENUM'       => $order->bill_telephone,
		'CURRENCYCODE'   => DEFAULT_CURRENCY,
		'SHIPTONAME'     => $order->ship_primary_name,
		'SHIPTOSTREET'   => $order->ship_address1,
		'SHIPTOSTREET2'  => $order->ship_address2,
		'SHIPTOCITY'     => $order->ship_city_town,
		'SHIPTOSTATE'    => $order->ship_state_province,
		'SHIPTOZIP'      => preg_replace("/[^A-Za-z0-9]/", "", $order->ship_postal_code),
		'SHIPTOCOUNTRY'  => $order->ship_country_code,
		'SHIPTOPHONENUM' => $order->ship_telephone,
	);

    // concatenate the submission data and put into $data variable
	$data = ''; // initiate XML string
    while(list($key, $value) = each($submit_data)) {
    	if ($value <> '') $data .= '&' . $key . '=' . urlencode($value);
    }

	// Execute the API operation; see the PPHttpPost function above.
	if (!$httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $data)) return true; // failed cURL

    $this->transaction_id = $httpParsedResponseAr['TRANSACTIONID'];
	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
		$messageStack->add(sprintf(MODULE_PAYMENT_PAYPAL_NVP_SUCCESSE_CODE, $httpParsedResponseAr['ACK'], $this->transaction_id, $this->cvv_codes[$httpParsedResponseAr['CVV2MATCH']]), 'success');
		$messageStack->add('Address verification results: ' . $this->avs_codes[$httpParsedResponseAr['AVSCODE']], 'success');
//echo 'Success response:'; print_r($httpParsedResponseAr); echo '<br>';
		return false;
	}
    $messageStack->add(MODULE_PAYMENT_PAYPAL_NVP_DECLINE_CODE . $httpParsedResponseAr['L_ERRORCODE0'] . ': ' . urldecode($httpParsedResponseAr['L_LONGMESSAGE0']) . ' - ' . MODULE_PAYMENT_CC_TEXT_DECLINED_MESSAGE, 'error');
//echo 'Failed response:'; print_r($httpParsedResponseAr); echo '<br>';
	return true;
  }

  function PPHttpPost($methodName_, $nvpStr_) {
	// Set up your API credentials, PayPal end point, and API version.
    $submit_data = array(
		'METHOD'    => $methodName_,
		'VERSION'   => '52.0',
		'PWD'       => MODULE_PAYMENT_PAYPAL_NVP_PW,
		'USER'      => MODULE_PAYMENT_PAYPAL_NVP_USER_ID,
		'SIGNATURE' => MODULE_PAYMENT_PAYPAL_NVP_SIG,
		'IPADDRESS' => get_ip_address(),
	);

	$data = ''; // initiate XML string
    while(list($key, $value) = each($submit_data)) {
    	if ($value <> '') $data .= '&' . $key . '=' . urlencode($value);
    }
	$data .= substr($data, 1) . $nvpStr_; // build the submit string

	if("sandbox" === MODULE_PAYMENT_PAYPAL_NVP_TESTMODE || "beta-sandbox" === MODULE_PAYMENT_PAYPAL_NVP_TESTMODE) {
		$API_Endpoint = MODULE_PAYMENT_PAYPAL_NVP_SANDBOX_SIG_URL;
	} else {
		$API_Endpoint = MODULE_PAYMENT_PAYPAL_NVP_LIVE_SIG_URL;
	}
	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//echo 'string = ' . $data . '<br>';
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		$messageStack->add('XML Read Error (cURL) #' . curl_errno($ch) . '. Description = ' . curl_error($ch),'error');
		return false;
//		exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
	}
	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);
	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}
	if (0 == sizeof($httpParsedResponseAr) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		$messageStack->add('PayPal Response Error.','error');
		return false;
//		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}
	return $httpParsedResponseAr;
  }
}
?>
