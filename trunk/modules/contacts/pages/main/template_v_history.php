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
//  Path: /modules/contacts/pages/main/template_v_history.php
//
?>
<div id="cat_history" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_HISTORY; ?></h2>

<?php // ***********************  History Section  ****************************** ?>
  <fieldset class="formAreaTitle">
    <legend><?php echo ACT_ACT_HISTORY; ?></legend>
    <table width="100%" cellspacing="6" cellpadding="0">
	  <tr>
	    <td width="50%"><?php echo constant('ACT_' . strtoupper($type) . '_FIRST_DATE') . ' ' . gen_locale_date($cInfo->first_date); ?></td>
	  </tr>
	  <tr>
	    <td width="50%"><?php echo constant('ACT_' . strtoupper($type) . '_LAST_DATE1') . ' ' . gen_locale_date($cInfo->last_update); ?></td>
	  </tr>
	</table>
  </fieldset>

  <fieldset class="formAreaTitle">
    <legend><?php echo ACT_ORDER_HISTORY; ?></legend>
    <table width="100%" cellspacing="6" cellpadding="0">
	  <tr><td valign="top" width="50%">
		<table border="1" width="100%" cellspacing="2" cellpadding="2">
		  <tr><th colspan="5"><?php echo sprintf(ACT_PO_HIST, LIMIT_HISTORY_RESULTS); ?></th></tr>
		  <tr><th><?php echo ACT_SO_NUMBER; ?></th>
		  <th><?php echo ACT_PO_NUMBER; ?></th>
		  <th><?php echo TEXT_DATE; ?></th>
		  <th><?php echo TEXT_OPEN; ?></th>
		  <th><?php echo TEXT_AMOUNT; ?></th></tr>
      <?php // first SO/PO
	    $result = load_open_orders($cInfo->id, '4', false, LIMIT_HISTORY_RESULTS);
		if ($result) {
		  array_shift($result); // the first entry is for new stuff, don't display
		  foreach ($result as $value) {
		    echo '<tr><td><a href="' . html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=orders&amp;action=edit&amp;jID=' . ($type == 'v' ? 4 : 10) . '&amp;oID=' . $value['id'], 'SSL') . '">' . $value['purchase_invoice_id'] . '</a></td>';
		    echo '<td>' . ($value['purch_order_id'] ? $value['purch_order_id'] : '&nbsp;') . '</td>';
		    echo '<td align="center">' . gen_locale_date($value['post_date']) . '</td>';
		    echo '<td align="center">' . ($value['closed'] ? '&nbsp;' : TEXT_YES) . '</td>';
		    echo '<td align="right">' . $currencies->format($value['total_amount']) . '</td></tr>';
		  }
		} else {
		  echo '<tr><td align="center" colspan="5">' . ACT_NO_RESULTS . '</td></tr>';
		}
	  ?>
		</table>
	  </td><td valign="top" width="50%">
		<table border="1" width="100%" cellspacing="2" cellpadding="2">
		  <tr><th colspan="5"><?php echo sprintf(ACT_INV_HIST, LIMIT_HISTORY_RESULTS); ?></th></tr>
		  <tr><th><?php echo ACT_INV_NUMBER; ?></th>
		  <th><?php echo ACT_PO_NUMBER; ?></th>
		  <th><?php echo TEXT_DATE; ?></th>
		  <th><?php echo TEXT_PAID; ?></th>
		  <th><?php echo TEXT_AMOUNT; ?></th></tr>
      <?php // then Sales/Purchases
	    $result = load_open_orders($cInfo->id, '6, 7, 21', false, LIMIT_HISTORY_RESULTS);
		if ($result) {
		  array_shift($result); // the first entry is for new stuff, don't display
		  foreach ($result as $value) {
		    $closed = $value['closed_date'] <> '0000-00-00' ? gen_locale_date($value['closed_date']) : ($value['closed'] ? TEXT_YES : '&nbsp;');
		    echo '<tr><td><a href="'   . html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=orders&amp;action=edit&amp;jID=' . $value['journal_id'] . '&amp;oID=' . $value['id'], 'SSL') . '">' . $value['purchase_invoice_id'] . '</a></td>';
		    echo '<td>' . ($value['purch_order_id'] ? $value['purch_order_id'] : '&nbsp;') . '</td>';
		    echo '<td align="center">' . gen_locale_date($value['post_date']) . '</td>';
		    echo '<td align="center">' . $closed . '</td>';
		    echo '<td align="right">'  . $currencies->format($value['total_amount']) . '</td></tr>';
		  }
		} else {
		  echo '<tr><td align="center" colspan="5">' . ACT_NO_RESULTS . '</td></tr>';
		}
	  ?>
		</table>
	  </td></tr>
    </table>
  </fieldset>
<?php echo RECORD_NUM_REF_ONLY . $cInfo->id; ?>

</div>