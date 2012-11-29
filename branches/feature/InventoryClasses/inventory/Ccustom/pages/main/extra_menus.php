<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010 PhreeSoft, LLC                   |
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
// |                                                                 |
// | The license that is bundled with this package is located in the |
// | file: /doc/manual/ch01-Introduction/license.html.               |
// | If not, see http://www.gnu.org/licenses/                        |
// +-----------------------------------------------------------------+
//  Path: /modules/inventory/custom/pages/main/extra_menus.php
//

// This file contains the extra defines that can be used for customizing you output and 
// adding functionality to PhreeBooks

// Modified Language defines, used to over-ride the standard language for customization. These
// values are loaded prior to the standard language defines and take priority.

// Additional Toolbar buttons

// Additional Action bar buttons (DYNAMIC AS IT IS SET BASED ON EVERY LINE!!!)
gen_pull_language('zencart');
function add_extra_action_bar_buttons($query_fields) {
  $output = '';
  if (defined('ZENCART_URL') && $_SESSION['admin_security'][SECURITY_ID_MAINTAIN_INVENTORY] > 1 && $query_fields['catalog'] == '1') {
    $output .= html_icon('../../../../modules/zencart/images/zencart.gif', ZENCART_IVENTORY_UPLOAD, 'small', 'onclick="submitSeq(' . $query_fields['id'] . ', \'upload\')"', '16', '16') . chr(10);
  }
  return $output;
}

// Defines used to increase search scope (additional fields) within a module, the constant 
// cannot change and the format should be as follows: 

// defines to use to retrieve more fields from sql for custom processing in list generation operations
$extra_fields = array();
// for the ZenCart upload mod, the catalog field should be in the table
if (defined('MODULE_ZENCART_STATUS')) $extra_fields[] = 'catalog';

if (count($extra_fields) > 0) $extra_query_list_fields = array_merge($extra_fields,$extra_query_list_fields);

?>