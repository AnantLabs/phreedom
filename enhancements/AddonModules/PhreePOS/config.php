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
//  Path: /modules/phreepos/config.php
//
// Release History
// 1.0 => 2011-04-15 - Initial Release
// 1.1 => rene added starting and closing line (admin main/js_include and language)
//        bugg fix added InventoryProp and processSkuProp to js_include, replaced ORD_TEXT_19_WINDOW_TITLE with MENU_HEADING_PHREEPOS
// 3.3 => see change_log.txt  
// Module software version information
define('MODULE_PHREEPOS_VERSION', '3.3');
// Menu Sort Positions
//define('MENU_HEADING_PHREEPOS_ORDER', 40);
// Menu Security id's (refer to master doc to avoid security setting overlap)
define('SECURITY_ID_PHREEPOS',           38);
define('SECURITY_ID_POS_MGR',            39);
define('SECURITY_ID_CUSTOMER_DEPOSITS', 109);
define('SECURITY_ID_VENDOR_DEPOSITS',   110);
// New Database Tables
define('TABLE_PHREEPOS_TILLS',    DB_PREFIX . 'phreepos_tills');
if (defined('MODULE_PHREEPOS_STATUS')) {
/*
  // Set the title menu
  $pb_headings[MENU_HEADING_PHREEPOS_ORDER] = array(
    'text' => MENU_HEADING_PHREEPOS, 
    'link' => html_href_link(FILENAME_DEFAULT, 'module=phreepos&amp;page=main&amp;mID=cat_pos', 'SSL'),
  );
*/
  // Set the menus
  $menu[] = array(
    'text'        => BOX_PHREEPOS,
    'heading'     => MENU_HEADING_CUSTOMERS, // MENU_HEADING_PHREEPOS
    'rank'        => 50,
    'security_id' => SECURITY_ID_PHREEPOS,
    'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreepos&amp;page=main', 'SSL'),
  );
  $menu[] = array(
    'text'        => BOX_POS_MGR, 
    'heading'     => MENU_HEADING_BANKING, 
    'rank'        => 53, 
    'security_id' => SECURITY_ID_POS_MGR, 
    'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreepos&amp;page=pos_mgr&amp;list=1', 'SSL'),
  );
  $menu[] = array(
    'text'        => BOX_CUSTOMER_DEPOSITS,
    'heading'     => MENU_HEADING_BANKING,
    'rank'        => 10,
    'security_id' => SECURITY_ID_CUSTOMER_DEPOSITS,
    'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreepos&amp;page=deposit&amp;type=c', 'SSL'),
  );
  $menu[] = array(
    'text'        => BOX_VENDOR_DEPOSITS,
    'heading'     => MENU_HEADING_BANKING,
    'rank'        => 50,
    'security_id' => SECURITY_ID_VENDOR_DEPOSITS,
    'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreepos&amp;page=deposit&amp;type=v', 'SSL'),
  );
  
}

?>