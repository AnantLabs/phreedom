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
//  Path: /modules/payment/methods/cod.php
//

class cod {
  var $code, $payment_fields;
  function cod() {
    $this->code = 'cod';
    // set the description for the journal_item record
	$this->payment_fields = implode(':', array($_POST['bill_primary_name'], $_POST['cod_field_1']));
  }

  function keys() {
    return array(
	  array('key' => 'MODULE_PAYMENT_COD_SORT_ORDER', 'default' => '40'),
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
    return array(
	  'id'     => $this->code,
      'page' => MODULE_PAYMENT_COD_TITLE,
	);
  }

  function pre_confirmation_check() {
    return false;
  }

  function before_process() {
    return false;
  }
}
?>