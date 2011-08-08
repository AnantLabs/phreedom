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
//  Path: /modules/shipping/pages/popup_label_viewer/pre_process.php
//
$security_level = validate_user(0, true);
/**************   page specific initialization  *************************/
require_once(DIR_FS_WORKING . 'defaults.php');

/**************   page specific initialization  *************************/
$method = $_GET['method'];
$date   = $_GET['date'];
$labels = $_GET['labels'];
$labels = explode(':',$labels);
if (count($labels) == 0) die('No labels were passed to label_viewer.php!');
$row_size   = intval(100 / count($labels));
$row_string = '';
for ($i = 0; $i < count($labels); $i++) $row_string .= $row_size . '%,';
$row_string = substr($row_string, 0, -1);

$file_path    = SHIPPING_DEFAULT_LABEL_DIR . $method . '/' . str_replace('-', '/', $date) . '/';
$browser_path = DIR_WS_MY_FILES . $_SESSION['company'] . '/shipping/labels/' . $method . '/' . str_replace('-', '/', $date) . '/';

$custom_html      = true; // need custom header to support frames
$include_header   = false;
$include_footer   = false;
$include_tabs     = false;
$include_calendar = false;
$include_template = 'template_main.php';

?>