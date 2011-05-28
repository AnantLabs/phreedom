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
//  Path: /modules/inventory/config.php
//
// Release History
// 3.0 => 2011-01-15 - Converted from stand-alone PhreeBooks release
// 3.1 => 2011-04-15 - Bug fixes
// Module software version information
define('MODULE_INVENTORY_VERSION',     '3.1');
// Menu Sort Positions
define('MENU_HEADING_INVENTORY_ORDER',    30);
// Menu Security id's (refer to master doc to avoid security setting overlap)
define('SECURITY_ID_PRICE_SHEET_MANAGER', 88);
define('SECURITY_ID_ADJUST_INVENTORY',   152);
define('SECURITY_ID_ASSEMBLE_INVENTORY', 153);
define('SECURITY_ID_MAINTAIN_INVENTORY', 151);
define('SECURITY_ID_TRANSFER_INVENTORY', 156);
// New Database Tables
define('TABLE_INVENTORY',                DB_PREFIX . 'inventory');
define('TABLE_INVENTORY_ASSY_LIST',      DB_PREFIX . 'inventory_assy_list');
define('TABLE_INVENTORY_COGS_OWED',      DB_PREFIX . 'inventory_cogs_owed');
define('TABLE_INVENTORY_COGS_USAGE',     DB_PREFIX . 'inventory_cogs_usage');
define('TABLE_INVENTORY_HISTORY',        DB_PREFIX . 'inventory_history');
define('TABLE_INVENTORY_MS_LIST',        DB_PREFIX . 'inventory_ms_list');
define('TABLE_INVENTORY_SPECIAL_PRICES', DB_PREFIX . 'inventory_special_prices');
define('TABLE_PRICE_SHEETS',             DB_PREFIX . 'price_sheets');
// Set the title menu
$pb_headings[MENU_HEADING_INVENTORY_ORDER] = array(
  'text' => MENU_HEADING_INVENTORY, 
  'link' => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;mID=cat_inv', 'SSL'),
);
// Set the menus
$menu[] = array(
  'text'        => BOX_INV_MAINTAIN, 
  'heading'     => MENU_HEADING_INVENTORY, 
  'rank'        => 5, 
  'security_id' => SECURITY_ID_MAINTAIN_INVENTORY, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=inventory&amp;page=main&amp;list=1', 'SSL'),
);
$menu[] = array(
  'text'        => ORD_TEXT_16_WINDOW_TITLE, 
  'heading'     => MENU_HEADING_INVENTORY, 
  'rank'        => 15, 
  'security_id' => SECURITY_ID_ADJUST_INVENTORY, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=inventory&amp;page=adjustments', 'SSL'),
);
$menu[] = array(
  'text'        => ORD_TEXT_14_WINDOW_TITLE, 
  'heading'     => MENU_HEADING_INVENTORY, 
  'rank'        => 20, 
  'security_id' => SECURITY_ID_ASSEMBLE_INVENTORY, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=inventory&amp;page=assemblies', 'SSL'),
);
if (ENABLE_MULTI_BRANCH) $menu[] = array(
  'text'        => BOX_INV_TRANSFER, 
  'heading'     => MENU_HEADING_INVENTORY, 
  'rank'        => 80, 
  'security_id' => SECURITY_ID_TRANSFER_INVENTORY, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=inventory&amp;page=transfer', 'SSL'),
);
$menu[] = array(
  'text'        => BOX_PRICE_SHEET_MANAGER,
  'heading'     => MENU_HEADING_CUSTOMERS,
  'rank'        => 65, 
  'security_id' => SECURITY_ID_PRICE_SHEET_MANAGER, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=inventory&amp;page=price_sheets&amp;list=1', 'SSL'),
);

?>