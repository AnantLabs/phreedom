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
//  Path: /modules/phreedom/pages/admin/template_tab_countries.php
//
$countries_toolbar = new toolbar;
$countries_toolbar->icon_list['cancel']['show'] = false;
$countries_toolbar->icon_list['open']['show']   = false;
$countries_toolbar->icon_list['save']['show']   = false;
$countries_toolbar->icon_list['delete']['show'] = false;
$countries_toolbar->icon_list['print']['show']  = false;
if ($security_level > 1) $countries_toolbar->add_icon('new', 'onclick="loadPopUp(\'countries_new\', 0)"', $order = 10);
if ($countries->extra_buttons) $countries->customize_buttons($countries_toolbar);

?>
<div id="countries" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_COUNTRIES_TABS; ?></h2>
  <?php echo $countries_toolbar->build_toolbar(); ?>
  <div class="pageHeading"><?php echo $countries->title; ?></div>
  <div id="countries_content"><?php echo $countries->build_main_html(); ?></div>
</div>
