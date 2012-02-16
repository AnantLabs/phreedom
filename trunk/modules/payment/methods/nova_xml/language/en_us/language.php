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
//  Path: /modules/payment/methods/nova_xml/language/en_us/language.php
//

// Admin Configuration Items
define('MODULE_PAYMENT_NOVA_XML_TEXT_TITLE', 'Elevon');
define('MODULE_PAYMENT_NOVA_XML_TEXT_DESCRIPTION','Accept credit card payments through the Elevon payment gateway');
define('MODULE_PAYMENT_NOVA_XML_TEXT_INTRODUCTION', 'When in test mode, cards return a success code but are not processed.');

define('MODULE_PAYMENT_NOVA_XML_STATUS_DESC', 'Do you want to accept Elevon Network payments ?');
define('MODULE_PAYMENT_NOVA_XML_MERCHANT_ID_DESC', 'The Elevon Merchant ID used for the Elevon Network service:');
define('MODULE_PAYMENT_NOVA_XML_USER_ID_DESC', 'The Elevon User ID used for the Elevon Network service:');
define('MODULE_PAYMENT_NOVA_XML_PIN_DESC', 'PIN used for the Elevon Network service:');
define('MODULE_PAYMENT_NOVA_XML_TESTMODE_DESC', 'Transaction mode used for processing orders');
define('MODULE_PAYMENT_NOVA_XML_AUTHORIZATION_TYPE_DESC', 'Do you want submitted credit card transactions to be authorized only, or authorized and captured?');
define('MODULE_PAYMENT_NOVA_XML_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');

// Catalog Items
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CATALOG_TITLE', 'Credit Card');  // Payment option title as displayed to the customer
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CREDIT_CARD_TYPE', 'Credit Card Type:');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_CVV', 'CVV Number (<a href="javascript:popupWindowCvv()">' . 'More Info' . '</a>)');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_JS_CC_CVV', '* The 3 or 4 digit CVV number must be entered from the back of the credit card.\n');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_DECLINED_MESSAGE', 'The credit card transaction did not process correctly. If no reason is given, the card was declined by the bank.');
  define('MODULE_PAYMENT_NOVA_XML_NO_DUPS','The credit card was not processed because it has already been processed. To recharge a credit card, the credit card must be valid and not contain any * characters.');
  define('MODULE_PAYMENT_NOVA_XML_TEXT_ERROR', 'Credit Card Error!');
?>