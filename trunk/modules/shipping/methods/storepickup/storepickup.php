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
//  Path: /modules/shipping/methods/storepickup/storepickup.php
//

class storepickup {
  function storepickup() {
    $this->code = 'storepickup';
  }

  function keys() {
    return array(
	  array('key' => 'MODULE_SHIPPING_STOREPICKUP_TITLE',      'default' => 'Store Pickup'),
	  array('key' => 'MODULE_SHIPPING_STOREPICKUP_COST',       'default' => '0.00'),
	  array('key' => 'MODULE_SHIPPING_STOREPICKUP_SORT_ORDER', 'default' => '35'),
	);
  }

  function update() {
    foreach ($this->keys() as $key) {
	  $field = strtolower($key['key']);
	  if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
	}
  }

  function quote($pkg = '') {
	$quote    = array();
	$arrRates = array();
	$methods  = array('1DEam','1Dam','1Dpm','2Dpm','3Dpm','GND','GDR');
	foreach ($methods as $value) {
	  $arrRates[$this->code][$value]['book']  = MODULE_SHIPPING_STOREPICKUP_COST;
	  $arrRates[$this->code][$value]['quote'] = MODULE_SHIPPING_STOREPICKUP_COST;
	  $arrRates[$this->code][$value]['cost']  = MODULE_SHIPPING_STOREPICKUP_COST;
	}
	$quote['result'] = 'success';
	$quote['rates']  = $arrRates;
	return $quote;
  }

}
?>