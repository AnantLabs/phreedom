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
//  Path: /modules/payment/pages/admin/template_tab_general.php
//

?>
<div id="general" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_GENERAL_SETTINGS; ?></h2>
  <fieldset class="formAreaTitle">
    <table border="0" width="100%" cellspacing="1" cellpadding="1">
	  <tr><th colspan="2"><?php echo TEXT_PAYMENT_DEFAULTS; ?></th></tr>
	  <tr>
	    <td><?php echo 'Description Here'; ?></td>
	    <td><?php echo html_pull_down_menu('payment_default_something', $sel_yes_no, $_POST['payment_default_something'] ? $_POST['payment_default_something'] : PAYMENT_DEFAULT_SOMETHING, ''); ?></td>
	  </tr>
	</table>
  </fieldset>
</div>
