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
//  Path: /modules/payment/classes/payment.php
//
// Revision history
// 2012-05-11 - Created
gen_pull_language('payment');
class payment {
  public $code;
  public $payment_fields;
  public $title;
  public $description;
  public $open_pos_drawer = false;
  public $show_in_pos	  = true;
  public $pos_gl_acct;
  public $sort_order;
  public $key             = array(); 
	 
  public function __construct(){
  	define('FILENAME_POPUP_CVV_HELP', 'popup_cvv_help');
	$this->code = get_called_class();
	$this->open_pos_drawer  = defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER')  ? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER')  : $this->open_pos_drawer;
	$this->sort_order  		= defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER')  		? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER')  	 : $this->sort_order;
	$this->pos_gl_acct 		= defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT') 		? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT') 	 : $this->pos_gl_acct;
	$this->show_in_pos      = defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SHOW_IN_POS')      ? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SHOW_IN_POS')      : $this->show_in_pos;
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER', 'default' => $this->open_pos_drawer, 'text' => OPEN_POS_DRAWER_DESC );
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER', 	   'default' => $this->sort_order,      'text' => SORT_ORDER_DESC);
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT', 	   'default' => $this->pos_gl_acct,     'text' => POS_GL_ACCT_DESC);
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SHOW_IN_POS', 	   'default' => $this->show_in_pos,     'text' => SHOW_IN_POS_DESC);
	$this->field_0 = $_POST[get_called_class().'_field_0'];//$this->cc_card_owner_last
	$this->field_1 = $_POST[get_called_class().'_field_1'];//$this->cc_card_number
	$this->field_2 = $_POST[get_called_class().'_field_2'];//$this->cc_expiry_month
	$this->field_3 = $_POST[get_called_class().'_field_3'];//$this->cc_expiry_year
	$this->field_4 = $_POST[get_called_class().'_field_4'];//$this->cc_cvv2
	$this->field_5 = $_POST[get_called_class().'_field_5'];//$this->cc_card_owner_first
	$card_number = trim($this->field_1);
	$card_number = substr($card_number, 0, 4) . '********' . substr($card_number, -4);
	$this->payment_fields = implode(':', array($this->field_0, $card_number, $this->field_2, $this->field_3, $this->field_4));
  }
	 
  function update() {
    foreach ($this->keys() as $key) {
	  $field = strtolower($key['key']);
	  if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
	}
  }
  
  function configure($key) {
    switch ($key) {
    	case 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER':
    		$temp = array(
		  		array('id' => '0', 'text' => TEXT_NO),
		  		array('id' => '1', 'text' => TEXT_YES),
	    	);
	    	return html_pull_down_menu(strtolower($key), $temp, constant($key));
	    case 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SHOW_IN_POS':
    		$temp = array(
		  		array('id' => '0', 'text' => TEXT_NO),
		  		array('id' => '1', 'text' => TEXT_YES),
	    	);
	    	return html_pull_down_menu(strtolower($key), $temp, constant($key));
    	case 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT':
    		return html_pull_down_menu(strtolower($key), gen_coa_pull_down(), constant($key));
    	default:
    		return html_input_field(strtolower($key), constant($key));
    }
  }
  
  function selection() {
    return array(
	  'id'   => get_called_class(),
      'page' => $this->title,
	);
  }
  
  function keys() {
  	return $this->key;
  }
  
  function javascript_validation() {
    return false;
  }

  function pre_confirmation_check() {
    return false;
  }

  function before_process() {
    return false;
  }
  
  function confirmation() {
    return array('title' => $this->description);
  }
  
  function getsortorder(){
  	if(!defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER')){
  		return $this->sort_order;
  	} else { 
  		return constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER'); 
  	}
  }

  function validate() {
    $this->cc_card_number = preg_replace("/[^0-9]/", '', $this->field_1);
    if (preg_match('/^4[0-9]{15}$/', $this->cc_card_number)) {
      $this->cc_card_type = 'Visa';
    } elseif (preg_match('/^5[1-5]{1}[0-9]{14}$/', $this->cc_card_number)) {
      $this->cc_card_type = 'Master Card';
    } elseif (preg_match('/^3[47]{1}[0-9]{13}$/', $this->cc_card_number)) {
      $this->cc_card_type = 'American Express';
    } elseif (preg_match('/^6011[0-9]{12}$/', $this->cc_card_number)) {
      $this->cc_card_type = 'Discover';
    } elseif (preg_match('/^[0-9]{15,16}$/', $this->cc_card_number)) {
      $this->cc_card_type = 'Other Credit Card';
    } else {
      return -1;
    }
    if (is_numeric($this->field_2) && ($this->field_2 > 0) && ($this->field_2 < 13)) {
      $this->cc_expiry_month = $this->field_2;
    } else {
      return -2;
    }
    $current_year = date('Y');
    if (strlen($this->field_3) == 2) $this->field_3 = '20'.$this->field_3;
    if (is_numeric($this->field_3) && ($this->field_3 >= $current_year) && ($this->field_3 <= ($current_year + 10))) {
      $this->cc_expiry_year = $this->field_3;
    } else {
      return -3;
    }
    if ($this->field_3 == $current_year) {
      if ($this->field_2 < date('n')) {
        return -4;
      }
    }
    return $this->is_valid();
  }

  function is_valid() {
    $cardNumber = strrev($this->cc_card_number);
    $numSum = 0;
    for ($i=0; $i<strlen($cardNumber); $i++) {
      $currentNum = substr($cardNumber, $i, 1);
      // Double every second digit
      if ($i % 2 == 1) {
        $currentNum *= 2;
      }
      // Add digits of 2-digit numbers together
      if ($currentNum > 9) {
        $firstNum = $currentNum % 10;
        $secondNum = ($currentNum - $firstNum) / 10;
        $currentNum = $firstNum + $secondNum;
      }
      $numSum += $currentNum;
    }
    // If the total has no remainder it's OK
    return ($numSum % 10 == 0);
  }
}
?>