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
  public $pos_gl_acct;
  public $sort_order;
  public $key             = array(); 
	 
  public function __construct(){
  	define('FILENAME_POPUP_CVV_HELP', 'popup_cvv_help');
	$this->code = get_called_class();
	$this->open_pos_drawer  = defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER')  ? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER')  : $this->open_pos_drawer;
	$this->sort_order  		= defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER')  		? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER')  	 : $this->sort_order;
	$this->pos_gl_acct 		= defined('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT') 		? constant('MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT') 	 : $this->pos_gl_acct;
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_OPEN_POS_DRAWER', 'default' => $this->open_pos_drawer, 'text' => OPEN_POS_DRAWER_DESC );
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_SORT_ORDER', 	   'default' => $this->sort_order,      'text' => SORT_ORDER_DESC);
	$this->key[] = array('key' => 'MODULE_PAYMENT_'.strtoupper(get_called_class()).'_POS_GL_ACCT', 	   'default' => $this->pos_gl_acct,    'text' => POS_GL_ACCT_DESC);
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
}
?>