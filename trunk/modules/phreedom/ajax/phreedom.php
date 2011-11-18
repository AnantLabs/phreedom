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
//  Path: /modules/phreedom/ajax/phreedom.php
//
/**************   Check user security   *****************************/
$security_level = validate_ajax_user();
/**************  include page specific files    *********************/
/**************   page specific initialization  *************************/
$xml    = NULL;
$action = $_GET['action'];
switch ($action) {
	case 'pull_colors':
		$theme = $_GET['theme'];
		$contents = scandir(DIR_FS_ADMIN.'themes/'.$theme.'/css/');
		include(DIR_FS_ADMIN.'themes/'.$theme.'/config.php');
		foreach ($contents as $color) {
			if ($color <> '.' && $color <> '..' && is_dir(DIR_FS_ADMIN.DIR_WS_THEMES.'/css/'.$color)) {
				$xml .= '<color>'.chr(10);
				$xml .= xmlEntry('id',   $color);
				$xml .= xmlEntry('text', $color);
				$xml .= '</color>'.chr(10);
			}
		}
		foreach ($theme_menu_options as $key => $value) {
			$xml .= '<menu>'.chr(10);
			$xml .= xmlEntry('id',   $key);
			$xml .= xmlEntry('text', $value);
			$xml .= '</menu>'.chr(10);
		}
		break;
	default: die;
}
echo createXmlHeader() . $xml . createXmlFooter();
die;
?>