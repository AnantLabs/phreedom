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
//  Path: /modules/shipping/methods/item/item.php
//

class item {
  function item() {
    $this->code = 'item';
  }

  function keys() {
    return array(
	  array('key' => 'MODULE_SHIPPING_ITEM_TITLE',      'default' => 'Item Shipping'),
	  array('key' => 'MODULE_SHIPPING_ITEM_COST',       'default' => '2.50'),
	  array('key' => 'MODULE_SHIPPING_ITEM_HANDLING',   'default' => '0.00'),
	  array('key' => 'MODULE_SHIPPING_ITEM_SORT_ORDER', 'default' => '30'),
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
	if ($pkg->pkg_item_count) {
	  $methods = array('1DEam','1Dam','1Dpm','2Dpm','3Dpm','GND','GDR');
	  foreach ($methods as $value) {
		$arrRates[$this->code][$value]['book']  = ($pkg->pkg_item_count * MODULE_SHIPPING_ITEM_COST) + MODULE_SHIPPING_ITEM_HANDLING;
		$arrRates[$this->code][$value]['quote'] = ($pkg->pkg_item_count * MODULE_SHIPPING_ITEM_COST) + MODULE_SHIPPING_ITEM_HANDLING;
		$arrRates[$this->code][$value]['cost']  = ($pkg->pkg_item_count * MODULE_SHIPPING_ITEM_COST) + MODULE_SHIPPING_ITEM_HANDLING;
	  }
    }
	$quote['result'] = 'success';
	$quote['rates'] = $arrRates;
	return $quote;
  }
}
?>