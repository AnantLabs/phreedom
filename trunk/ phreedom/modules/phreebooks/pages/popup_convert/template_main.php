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
//  Path: /modules/phreebooks/pages/popup_convert/template_main.php
//

// start the form
echo html_form('popup_convert', FILENAME_DEFAULT, gen_get_all_get_params(array('action'))) . chr(10);

// include hidden fields
echo html_hidden_field('todo', '') . chr(10);
echo html_hidden_field('id',  $id) . chr(10);

// customize the toolbar actions
$toolbar->icon_list['cancel']['params'] = 'onclick="self.close()"';
$toolbar->icon_list['open']['show']     = false;
$toolbar->icon_list['save']['params']   = 'onclick="submitToDo(\'save\')"';
$toolbar->icon_list['delete']['show']   = false;
$toolbar->icon_list['print']['show']    = false;

// pull in extra toolbar overrides and additions
if (count($extra_toolbar_buttons) > 0) {
	foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
}

// add the help file index and build the toolbar
switch ($account_type) {
  case 'c': $toolbar->add_help('07.03.02.04'); break;
  case 'v': $toolbar->add_help('07.02.02.04'); break;
}
if ($search_text) $toolbar->search_text = $search_text;
echo $toolbar->build_toolbar(); 

// Build the page
?>
<div class="pageHeading"><?php echo $jID == 3 ? ORD_CONVERT_TO_RFQ_PO : ORD_CONVERT_TO_SO_INV; ?></div>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr>
	<td class="main"> <?php echo html_radio_field('conv_type', 'so', true, '', '') . ($jID == 3 ? ORD_CONVERT_TO_PO : ORD_CONVERT_TO_SO) . chr(10); ?></td>
	<td class="main"><?php echo ($jID == 3 ? ORD_HEADING_NUMBER_4 : ORD_HEADING_NUMBER_10) . html_input_field('so_num', $so_num, '') . '<br />' . chr(10); ?></td>
  </tr>
<?php if ($jID <> 3) { ?>
  <tr>
	<td class="main"><?php echo html_radio_field('conv_type', 'inv', false, '', '') . ORD_CONVERT_TO_INV . '<br />' . chr(10); ?></td>
	<td class="main"><?php echo ORD_TEXT_12_NUMBER . html_input_field('inv_num', $inv_num, '') . '<br />' . chr(10); ?></td>
  </tr>
<?php } ?>
</table>
<?php echo ORD_SO_INV_MESSAGE; ?> 
</form>