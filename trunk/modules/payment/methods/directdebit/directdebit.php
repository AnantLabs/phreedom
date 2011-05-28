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
//  Path: /modules/payment/methods/directdebit/directdebit.php
//

class directdebit {
  var $code, $payment_fields;

  function directdebit() {
    $this->code = 'directdebit';
    $this->payment_fields = implode(':', array($_POST['directdebit_field_0']));
  }

  function keys() {
    return array(
      array('key' => 'MODULE_PAYMENT_DIRECTDEBIT_SORT_ORDER', 'default' => '35'),
    );
  }

  function update() {
    foreach ($this->keys() as $key) {
      $field = strtolower($key['key']);
      if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
    }
  }

  function javascript_validation() {
    return false;
  }

  function selection() {
    global $order;
    return array(
		'id'     => $this->code,
		'page' => MODULE_PAYMENT_DIRECTDEBIT_TEXT_TITLE,
		'fields' => array(
			array(
			  'title' => MODULE_PAYMENT_DIRECTDEBIT_TEXT_REF_NUM,
			  'field' => html_input_field($this->code . '_field_0', $order->directdebit_ref, 'size="33" maxlength="32"'),
			),
		),
	);
  }

  function pre_confirmation_check() {
    return false;
  }

  function confirmation() {
    return array('title' => MODULE_PAYMENT_DIRECTDEBIT_TEXT_DESCRIPTION);
  }

  function before_process() {
    return false;
  }
}

?>