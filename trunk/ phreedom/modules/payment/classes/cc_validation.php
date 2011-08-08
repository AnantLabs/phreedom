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
//  Path: /modules/general/classes/cc_validation.php
//

/**
 * cc_validation Class.
 * Class to validate credit card numbers etc
 *
 * @package classes
 */
class cc_validation {
  var $cc_type, $cc_number, $cc_expiry_month, $cc_expiry_year;

  function validate($number, $expiry_m, $expiry_y) {
    $this->cc_number = preg_replace("/[^0-9]/", '', $number);

    if (preg_match('/^4[0-9]{15}$/', $this->cc_number)) {
      $this->cc_type = 'Visa';
    } elseif (preg_match('/^5[1-5]{1}[0-9]{14}$/', $this->cc_number)) {
      $this->cc_type = 'Master Card';
    } elseif (preg_match('/^3[47]{1}[0-9]{13}$/', $this->cc_number)) {
      $this->cc_type = 'American Express';
    } elseif (preg_match('/^6011[0-9]{12}$/', $this->cc_number)) {
      $this->cc_type = 'Discover';
    } elseif (preg_match('/^[0-9]{15,16}$/', $this->cc_number)) {
      $this->cc_type = 'Other Credit Card';
    } else {
      return -1;
    }
    if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
      $this->cc_expiry_month = $expiry_m;
    } else {
      return -2;
    }
    $current_year = date('Y');
    $expiry_y = substr($current_year, 0, 2) . $expiry_y;
    if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
      $this->cc_expiry_year = $expiry_y;
    } else {
      return -3;
    }
    if ($expiry_y == $current_year) {
      if ($expiry_m < date('n')) {
        return -4;
      }
    }
    return $this->is_valid();
  }

  function is_valid() {
    $cardNumber = strrev($this->cc_number);
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