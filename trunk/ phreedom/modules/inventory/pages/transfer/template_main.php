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
//  Path: /modules/inventory/pages/transfer/template_main.php
//
$hidden_fields = NULL;
// start the form
echo html_form('inv_xfer', FILENAME_DEFAULT, gen_get_all_get_params(array('action'))) . chr(10);
// include hidden fields
echo html_hidden_field('todo',     '') . chr(10);
echo html_hidden_field('inv_acct', '') . chr(10);
echo html_hidden_field('inv_type', '') . chr(10);
// customize the toolbar actions
$toolbar->icon_list['cancel']['params'] = 'onclick="location.href = \'' . html_href_link(FILENAME_DEFAULT, '', 'SSL') . '\'"';
$toolbar->icon_list['save']['params']   = 'onclick="submitToDo(\'save\')"';
if ($security_level < 2) $toolbar->icon_list['save']['show'] = false;
$toolbar->icon_list['open']['show']     = false;
$toolbar->icon_list['delete']['show']   = false;
$toolbar->icon_list['print']['show']    = false;
$toolbar->add_icon('new', 'onclick="location.href = \'' . html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL') . '\'"', $order = 2);
// pull in extra toolbar overrides and additions
if (count($extra_toolbar_buttons) > 0) {
	foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
}
// add the help file index and build the toolbar
$toolbar->add_help('07.04.02');
echo $toolbar->build_toolbar(); 
// Build the page
?>
<div class="pageHeading"><?php echo INV_POPUP_XFER_WINDOW_TITLE; ?></div>
<table align="center">
  <tr>
    <td>
      <table>
        <tr>
	      <td><?php echo INV_XFER_FROM_STORE . '&nbsp;' . html_pull_down_menu('source_store_id', gen_get_store_ids(), $cInfo->source_store_id ? $cInfo->source_store_id : $_SESSION['admin_prefs']['def_store_id']); ?></td>
	      <td><?php echo INV_XFER_TO_STORE . '&nbsp;' . html_pull_down_menu('dest_store_id', gen_get_store_ids(), $cInfo->dest_store_id); ?></td>
          <td align="right"><?php echo TEXT_POST_DATE . '&nbsp;'; ?></td>
	      <td><?php echo html_calendar_field($cal_xfr); ?></td>
        </tr>
        <tr>
	      <td colspan="2"><?php echo INV_ADJUSTMENT_ACCOUNT . '&nbsp;' . html_pull_down_menu('gl_acct', $gl_array_list, $cInfo->gl_acct ? $cInfo->gl_acct : INV_STOCK_DEFAULT_INVENTORY, ''); ?></td>
          <td align="right"><?php echo TEXT_REFERENCE; ?></td>
	      <td><?php echo html_input_field('purchase_invoice_id', $cInfo->purchase_invoice_id); ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table id="item_table" align="center">
        <tr>
	      <th><?php echo html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small'); ?></th>
	      <th><?php echo TEXT_QUANTITY; ?></th>
	      <th><?php echo INV_HEADING_QTY_IN_STOCK; ?></th>
	      <th><?php echo TEXT_BALANCE; ?></th>
	      <th><?php echo TEXT_SKU; ?></th>
	      <th><?php echo TEXT_DESCRIPTION; ?></th>
        </tr>
	<?php
	$i   = 1;
	$sku = 'sku_' . $i;
	if (!isset($cInfo->$sku)) {
	  $hidden_fields .= '<script type="text/javascript">addInvRow();</script>';
	} else {
	  while (true) {
		if (!isset($cInfo->$sku)) break;
		echo '  <tr>'   . chr(10);
		echo '    <td>' . html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . INV_MSG_DELETE_INV_ITEM . '\')) removeInvRow(' . $i . ');"') . '</td>' . chr(10);
		$qty = 'qty_' . $i;
		echo '    <td>' . html_input_field('qty_' . $i, $cInfo->$qty, 'size="6" maxlength="5" style="text-align:right" onchange="updateBalance()"') . '</td>' . chr(10);
		$stock = 'stock_' . $i;
		echo '    <td>' . html_input_field('stock_' . $i, $cInfo->$stock, 'readonly="readonly" size="6" maxlength="5" style="text-align:right"') . '</td>' . chr(10);
		$bal = 'balance_' . $i;
		echo '    <td>' . html_input_field('balance_' . $i, $cInfo->$bal, 'readonly="readonly" size="6" maxlength="5" style="text-align:right"') . '</td>' . chr(10);
		echo '    <td nowrap="nowrap">' . chr(10);
		$sku = 'sku_' . $i;
		echo html_input_field('sku_' . $i, $cInfo->$sku, 'size="' . (MAX_INVENTORY_SKU_LENGTH + 1) . '" maxlength="' . MAX_INVENTORY_SKU_LENGTH . '"') . '&nbsp;';
		echo html_icon('actions/system-search.png', TEXT_SEARCH, 'small', $params = 'align="top" style="cursor:pointer" onclick="InventoryList()"');
		echo html_icon('actions/tab-new.png', TEXT_SERIAL_NUMBER, 'small', 'align="top" style="cursor:pointer" onclick="serialList(\'serial_' . $i . '\')"');
// Hidden fields
		$serial = 'serial_' . $i;
		echo html_hidden_field('serial_' . $i, $cInfo->$serial) . chr(10);
		$acct   = 'serial_' . $i;
		echo html_hidden_field('acct_' . $i, $cInfo->$acct) . chr(10);
// End hidden fields
		echo '    </td>'. chr(10);
		$desc = 'desc_' . $i;
		echo '    <td>' . html_input_field('desc_' . $i, $cInfo->$desc, 'size="90"') . '</td>' . chr(10);
		echo '  </tr>'  . chr(10);
		$i++;
	  }
	} ?>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table>
        <tr>
          <td><?php echo html_icon('actions/list-add.png', TEXT_ADD, 'medium', 'onclick="addInvRow()"'); ?></td>
        </tr>
      </table>
    </td>
  </tr>
</table><?php // display the hidden fields that are not used in this rendition of the form
echo $hidden_fields;
?>
</form>
