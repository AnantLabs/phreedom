<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010 phreedom, LLC                   |
// | http://www.phreedom.com                                        |
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
//  Path: /modules/phreedom/config.php
//
// Release History
// 3.0 => 2011-01-15 - Converted from stand-alone PhreeBooks release
// 3.1 => 2011-04-15 - Bug fixes
// Module software version information
define('MODULE_PHREEDOM_VERSION',  '3.1');
// Menu Sort Positions
define('MENU_HEADING_COMPANY_ORDER',  90);
// Menu Security id's (refer to master doc to avoid security setting overlap)
define('SECURITY_ID_USERS',            1);
define('SECURITY_ID_IMPORT_EXPORT',    2);
define('SECURITY_ID_CONFIGURATION',   11); // admin for all modules
define('SECURITY_ID_BACKUP',          18);
define('SECURITY_ID_ENCRYPTION',      20);
// New Database Tables
define('TABLE_AUDIT_LOG',      DB_PREFIX . 'audit_log');
define('TABLE_CONFIGURATION',  DB_PREFIX . 'configuration');
define('TABLE_CURRENCIES',     DB_PREFIX . 'currencies');
define('TABLE_CURRENT_STATUS', DB_PREFIX . 'current_status');
define('TABLE_DATA_SECURITY',  DB_PREFIX . 'data_security');
define('TABLE_EXTRA_FIELDS',   DB_PREFIX . 'xtra_fields');
define('TABLE_EXTRA_TABS',     DB_PREFIX . 'xtra_tabs');
define('TABLE_USERS',          DB_PREFIX . 'users');
define('TABLE_USERS_PROFILES', DB_PREFIX . 'users_profiles');
// TBD Tables no longer in use, but need to verify conversion before delete
define('TABLE_IMPORT_EXPORT',  DB_PREFIX . 'import_export');
define('TABLE_REPORTS',        DB_PREFIX . 'reports');
define('TABLE_REPORT_FIELDS',  DB_PREFIX . 'report_fields');
define('TABLE_PROJECT_VERSION',DB_PREFIX . 'project_version');
// Set the title menu
$pb_headings[MENU_HEADING_COMPANY_ORDER] = array(
  'text' => MENU_HEADING_COMPANY, 
  'link' => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;mID=cat_company', 'SSL'));
// Set the menus
$menu[] = array(
  'text'        => BOX_HEADING_CONFIGURATION,
  'heading'     => MENU_HEADING_COMPANY,
  'rank'        => 1,
  'security_id' => SECURITY_ID_CONFIGURATION, 
  'hidden'      => $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION] > 0 ? false : true,
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=admin', 'SSL'),
);
if (DEBUG) $menu[] = array(
  'text'        => BOX_HEADING_DEBUG_DL,
  'heading'     => MENU_HEADING_TOOLS,
  'rank'        => 0,
  'hide'        => true,
  'security_id' => SECURITY_ID_CONFIGURATION,
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;action=debug', 'SSL'),
);
if (ENABLE_ENCRYPTION) $menu[] = array(
  'text'        => BOX_HEADING_ENCRYPTION,
  'heading'     => MENU_HEADING_TOOLS,
  'rank'        => 1,
  'security_id' => SECURITY_ID_ENCRYPTION,
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=encryption', 'SSL'),
);
$menu[] = array(
  'text'        => BOX_IMPORT_EXPORT, 
  'heading'     => MENU_HEADING_TOOLS, 
  'rank'        => 50, 
  'security_id' => SECURITY_ID_IMPORT_EXPORT, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=import_export', 'SSL'),
);
$menu[] = array(
  'text'        => BOX_HEADING_BACKUP,
  'heading'     => MENU_HEADING_TOOLS,
  'rank'        => 95,
  'security_id' => SECURITY_ID_BACKUP, 
  'hidden'      => $_SESSION['admin_security'][SECURITY_ID_BACKUP] > 3 ? false : true,
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=backup', 'SSL'),
);
$menu[] = array(
  'text'        => BOX_HEADING_USERS,
  'heading'     => MENU_HEADING_COMPANY,
  'rank'        => 90,
  'security_id' => SECURITY_ID_USERS, 
  'link'        => html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=users&amp;list=1', 'SSL'),
);

?>