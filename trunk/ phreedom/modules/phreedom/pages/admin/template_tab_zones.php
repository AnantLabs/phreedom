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
//  Path: /modules/phreedom/pages/admin/template_tab_zones.php
//
$zones_toolbar = new toolbar;
$zones_toolbar->icon_list['cancel']['show'] = false;
$zones_toolbar->icon_list['open']['show']   = false;
$zones_toolbar->icon_list['save']['show']   = false;
$zones_toolbar->icon_list['delete']['show'] = false;
$zones_toolbar->icon_list['print']['show']  = false;
if ($security_level > 1) $zones_toolbar->add_icon('new', 'onclick="loadPopUp(\'zones_new\', 0)"', $order = 10);
if ($zones->extra_buttons) $zones->customize_buttons($zones_toolbar);

?>
<div id="zones" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_ZONES_TABS; ?></h2>
  <?php echo $zones_toolbar->build_toolbar(); ?>
  <div class="pageHeading"><?php echo $zones->title; ?></div>
  <div id="zones_content"><?php echo $zones->build_main_html(); ?></div>
</div>
