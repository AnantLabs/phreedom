<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2010 PhreeSoft, LLC                               |
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
//  Path: /modules/inventory/custom/pages/main/extra_actions.php
//

	require_once(DIR_FS_WORKING . 'custom/pages/main/filter_extra_actions.php');
	require_once(DIR_FS_WORKING . 'custom/pages/main/extra_actions_zc.php');
	$extra_tax_rates = ord_calculate_tax_drop_down();
	
	$js_tax_rates = 'var tax_rates = new Array(' . count($extra_tax_rates) . ');' . chr(10);
	for ($i = 0; $i < count($extra_tax_rates); $i++) {
	  $js_tax_rates .= 'tax_rates[' . $i . '] = new salesTaxes("' . $extra_tax_rates[$i]['id'] . '", "' . $extra_tax_rates[$i]['text'] . '", "' . $extra_tax_rates[$i]['rate'] . '");' . chr(10);
	};
	
// sugestie voor nieuw artikel nummer	
	$action = isset($_GET['action']) ? $_GET['action'] : $_POST['todo'];
	if($action == 'new'){
		$result = $db->Execute("SELECT MAX(sku + 0) AS 'nieuwe' FROM " . TABLE_INVENTORY );
		$sku = $result->fields['nieuwe'] + 1 ;
	}

?>