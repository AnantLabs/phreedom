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
//  Path: /modules/shipping/custom/pages/popup_shipping/extra_defines.php
//

// This file contains the extra defines that can be used for customizing you output and 
// adding functionality to PhreeBooks

// Modified Language defines, used to over-ride the standard language for customization. These
// values are loaded prior to the standard language defines and take priority.
 define('SHIPPING_DEFAULT_LTL_CLASS','60'); // for batteries

// Additional Toolbar buttons

// Additional Action bar buttons (DYNAMIC AS IT IS SET BASED ON EVERY LINE!!!)

// Defines used to increase search scope (additional fields) within a module, the constant 
// cannot change and the format should be as follows: 

// defines to use to retrieve more fields from sql for custom processing in list generation operations

// Additional functions
define('MY_MARKUP',0.30); // percentage markup
define('MY_HANDLING_CHARGE',1.00); // fixed handling charge

function fedex_shipping_rate_calc($book_cost, $my_cost, $method = 'GND') {
  global $currencies;
  switch ($method) {
    case '1DEam': 
    case '1Dam': 
    case '1Dpm': 
    case '2Dpm': 
    case '3Dpm': 
    case 'GND': 
    case 'GDR': 
    case 'I2DEam': 
    case 'I2Dam': 
    case 'I3D': 
    case 'GndFrt':
	case 'EcoFrt':
	  $book = $currencies->clean_value($book_cost);
	  $cost = $currencies->clean_value($my_cost);
	  if ($cost > 0 && $cost > $book) $book = $cost; // case when cost greater than book
	  if ($book < 1 && $cost > 0) $book = $cost * (1 + MY_MARKUP); // case when book value not returned
	  $charge = $cost * (1 + MY_MARKUP);
	  return min($book, $charge) + MY_HANDLING_CHARGE;
	  break;
    case '1DFrt': 
    case '2DFrt': 
    case '3DFrt': 
	default:
	  return ''; // don't quote freight because we don't know if it can be delivered on time
  }
}

function ups_shipping_rate_calc($book_cost, $my_cost, $method = 'GND') {
  return fedex_shipping_rate_calc($book_cost, $my_cost, $method);
}
?>