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
//  Path: /modules/shipping/methods/table/table.php
//

class table {
  function table() {
    $this->code = 'table';
  }

  function keys() {
    return array(
	  array('key' => 'MODULE_SHIPPING_TABLE_TITLE',      'default' => 'Table Method'),
	  array('key' => 'MODULE_SHIPPING_TABLE_COST',       'default' => '25:8.50,50:5.50,10000:0.00'),
	  array('key' => 'MODULE_SHIPPING_TABLE_MODE',       'default' => 'weight'),
	  array('key' => 'MODULE_SHIPPING_TABLE_HANDLING',   'default' => '0.00'),
	  array('key' => 'MODULE_SHIPPING_TABLE_SORT_ORDER', 'default' => '40'),
	);
  }

  function configure($key) {
	switch ($key) {
	  case 'MODULE_SHIPPING_TABLE_MODE':
		$temp = array(
		  array('id' => 'price',  'text' => TEXT_PRICE),
		  array('id' => 'weight', 'text' => TEXT_WEIGHT),
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

  function quote($pkg = '') {
    if (MODULE_SHIPPING_TABLE_MODE == 'price') {
      $order_total = $pkg->pkg_total;
    } else { // weight
      $order_total = $pkg->pkg_weight;
    }
    $table_cost = split("[:,]", MODULE_SHIPPING_TABLE_COST);
    $size = sizeof($table_cost);
    for ($i= 0, $n = $size; $i < $n; $i += 2) {
      if ($order_total <= $table_cost[$i]) {
        $shipping = $table_cost[$i+1];
        break;
      }
    }
	$quote = array();
	$arrRates  = array();
	if ($pkg->pkg_item_count) {
	  $methods = array('1DEam','1Dam','1Dpm','2Dpm','3Dpm','GND','GDR');
	  foreach ($methods as $value) {
		$arrRates[$this->code][$value]['book']  = $shipping + MODULE_SHIPPING_TABLE_HANDLING;
		$arrRates[$this->code][$value]['quote'] = $shipping + MODULE_SHIPPING_TABLE_HANDLING;
		$arrRates[$this->code][$value]['cost']  = $shipping + MODULE_SHIPPING_TABLE_HANDLING;
	  }
	}
	$quote['result'] = 'success';
	$quote['rates']  = $arrRates;
	return $quote;
  }
}
?>