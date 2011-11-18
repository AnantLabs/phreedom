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
//  Path: /modules/contacts/pages/main/template_c_payment.php
//

?>
<div id="tab_payment">
  <fieldset>
    <legend><?php echo ACT_CATEGORY_P_ADDRESS; ?></legend>
    <table>
<?php
	if (sizeof($cInfo->pmt_values) > 0) {
		$field  = '<tr><td colspan="2"><table id="pmt_table" width="100%" cellspacing="0" cellpadding="0">';
		$field .= '<tr>' . chr(10);
		$field .= '  <th>' . ACT_CARDHOLDER_NAME . '</th>' . chr(10);
		$field .= '  <th>' . ACT_CARD_HINT . '</th>' . chr(10);
		$field .= '  <th>' . ACT_EXP . '</th>' . chr(10);
		$field .= '  <th align="center">' . TEXT_ACTION . '</th>' . chr(10);
		$field .= '</tr>' . chr(10) . chr(10);
		$cnt = 0;
		$odd = true;
		foreach ($cInfo->pmt_values as $payment) {
			$field .= '<tr id="trp_' . $payment['id'] . '" class="' . ($odd?'odd':'even') . '" style="cursor:pointer">';
			$field .= '  <td onclick="editPmtRow(' . $cnt . ')">' . $payment['name'] . '</td>' . chr(10);
			$field .= '  <td onclick="editPmtRow(' . $cnt . ')">' . $payment['hint'] . '</td>' . chr(10);
			$field .= '  <td onclick="editPmtRow(' . $cnt . ')">' . $payment['exp'] . '</td>' . chr(10);
			$field .= '  <td align="center">';
			$field .= html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 'onclick="editPmtRow(' . $cnt . ')"') . chr(10);
			$field .= '&nbsp;' . html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . ACT_WARN_DELETE_PAYMENT . '\')) removePmtRow(' . $payment['id'] . ');"') . chr(10);
			$field .= '  </td>' . chr(10);
			$field .= '</tr>' . chr(10) . chr(10);
			$cnt++;
			$odd = !$odd;
		}
		$field .= '</table></td></tr>' . chr(10);
		echo $field;
	}
    if (!$_SESSION['admin_encrypt']) { ?>
      <tr><td colspan="2" class="ui-state-highlight"><?php echo ACT_NO_ENCRYPT_KEY_ENTERED; ?></td></tr>
<?php } ?>
      <tr><td colspan="2"><?php echo '&nbsp;'; ?></td></tr>
      <tr><th colspan="2"><?php echo ACT_PAYMENT_MESSAGE; ?></th></tr>
	  <tr>
	    <td><?php echo ACT_CARDHOLDER_NAME; ?></td>
		<td><?php echo html_input_field('payment_cc_name', $cInfo->payment_cc_name, 'size="50" maxlength="48"'); ?>
		  <?php echo html_icon('actions/view-refresh.png', TEXT_RESET, 'small', 'onclick="clearPmtForm()"'); ?></td>
	  </tr>
	  <tr>
	    <td><?php echo ACT_PAYMENT_CREDIT_CARD_NUMBER; ?></td>
		<td><?php echo html_input_field('payment_cc_number', $cInfo->payment_cc_number, 'size="20" maxlength="19"'); ?></td>
	  </tr>
	  <tr>
	    <td><?php echo ACT_PAYMENT_CREDIT_CARD_EXPIRES; ?></td>
		<td><?php echo html_pull_down_menu('payment_exp_month', $expires_month, $cInfo->payment_exp_month) . '&nbsp;' . 
		    		   html_pull_down_menu('payment_exp_year', $expires_year, $cInfo->payment_exp_year); ?></td>
	  </tr>
	  <tr>
	    <td><?php echo ACT_PAYMENT_CREDIT_CARD_CVV2; ?></td>
		<td><?php echo html_input_field('payment_cc_cvv2', $cInfo->payment_cc_cvv2, 'size="5" maxlength="4"'); ?></td>
	  </tr>
    </table>
  </fieldset>
</div>
