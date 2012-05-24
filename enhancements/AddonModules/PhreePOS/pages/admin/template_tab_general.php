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
//  Path: /modules/phreepos/pages/admin/template_tab_general.php
//

?>
<div id="general" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_PHREEPOS_SETTINGS; ?></h2>
  <fieldset class="formAreaTitle">
	<table align="center">
	  <tr><th colspan="2"><?php echo MENU_HEADING_CONFIG; ?></th></tr> 
	  <tr>
		<td><?php echo PHREEPOS_REQUIRE_ADDRESS_DESC; ?></td>
		<td><?php echo html_pull_down_menu('phreepos_require_address', $sel_yes_no, $_POST['phreepos_require_address'] ? $_POST['phreepos_require_address'] : PHREEPOS_REQUIRE_ADDRESS, ''); ?></td>
	  </tr>
	  <tr>
		<td><?php echo PHREEPOS_RECEIPT_PRINTER_NAME_DESC; ?></td>
		<td><?php echo html_input_field('phreepos_receipt_printer_name', $_POST['phreepos_receipt_printer_name'] ? $_POST['phreepos_receipt_printer_name'] : PHREEPOS_RECEIPT_PRINTER_NAME, ''); ?></td>
	  </tr>
	  <tr>
		<td><?php echo PHREEPOS_RECEIPT_PRINTER_STARTING_LINE_DESC ?></td>
		<td><?php echo html_input_field('phreepos_receipt_printer_starting_line', $_POST['phreepos_receipt_printer_starting_line'] ? $_POST['phreepos_receipt_printer_starting_line'] : PHREEPOS_RECEIPT_PRINTER_STARTING_LINE, ''); ?></td>
	  </tr>	  
	  <tr>
		<td><?php echo PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE_DESC ?><br><a href="<?php echo DIR_WS_ADMIN.'modules/phreepos/printer_codes.htm'?>">opening drawer codes</a></td>
		<td><?php echo html_input_field('phreepos_receipt_printer_closing_line', $_POST['phreepos_receipt_printer_closing_line'] ? $_POST['phreepos_receipt_printer_closing_line'] : PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE, ''); ?></td>
	  </tr>  
	  <tr>
		<td><?php echo PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER_DESC ?></td>
		<td><?php echo html_input_field('phreepos_receipt_printer_open_drawer', $_POST['phreepos_receipt_printer_open_drawer'] ? $_POST['phreepos_receipt_printer_open_drawer'] : PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER, ''); ?></td>
	  </tr>  
	  <tr>
		<td><?php echo PHREEPOS_DISPLAY_WITH_TAX_DESC ?></td>
		<td><?php echo html_pull_down_menu('phreepos_display_with_tax', $sel_yes_no, $_POST['phreepos_display_with_tax'] ? $_POST['phreepos_display_with_tax'] : PHREEPOS_DISPLAY_WITH_TAX, '1'); ?></td>
	  </tr>
	  <tr>
		<td><?php echo PHREEPOS_DISCOUNT_OF_DESC ?></td>
		<td><?php echo html_pull_down_menu('phreepos_discount_of', $sel_yes_no, $_POST['phreepos_discount_of'] ? $_POST['phreepos_discount_of'] : PHREEPOS_DISCOUNT_OF, '0'); ?></td>
	  </tr>
	  <tr>
		<td><?php echo PHREEPOS_ROUNDING_DESC ?></td>
		<td><?php echo html_pull_down_menu('phreepos_rounding',    $sel_rounding, $_POST['phreepos_rounding'] ? $_POST['phreepos_rounding'] : PHREEPOS_ROUNDING, '0'); ?></td>
	  </tr>
	</table>
  </fieldset>
</div>
