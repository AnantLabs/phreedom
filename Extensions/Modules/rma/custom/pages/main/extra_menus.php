<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |

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
//  Path: /modules/inventory/custom/pages/main/extra_menus.php
//

define('TEXT_EVALUATION','Testing/Evaluation');
define('RMA_HEADING_EVALUATION','RMA Testing and Evaluation');
define('INVENTORY_EVALUATION_OFFSET',1000);
// add extra tabs
$extra_rma_tabs = array(array(
    'tab_id'       => 'tab_evaluation',
    'tab_title'    => TEXT_EVALUATION,
    'tab_filename' => 'tab_evaluation',
));

?>