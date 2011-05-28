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
//  Path: /modules/contacts/pages/main/template_v_general.php
//

// *********************** Display account information ****************************** 
$cal_v_gen = array(
  'name'      => 'dateReference',
  'form'      => 'contacts',
  'fieldname' => 'due_date',
  'imagename' => 'btn_v_gen',
  'default'   => '',
  'params'    => array('align' => 'left'),
);
?>
<script type="text/javascript"><?php echo js_calendar_init($cal_v_gen); ?></script>

<div id="cat_general" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_GENERAL; ?></h2>
  <fieldset class="formAreaTitle">
  <legend><?php echo ACT_CATEGORY_CONTACT; ?></legend>
  <table class="formArea" border="0" cellspacing="2" cellpadding="2">
    <tr>
     <td align="right" class="main">
	   <?php echo constant('ACT_' . strtoupper($type) . '_SHORT_NAME'); ?>
	   <?php echo $auto_type ? ' ' . ACT_ID_AUTO_FILL : ''; ?>
	 </td>
     <td class="main"><?php echo html_input_field('short_name', $cInfo->short_name, 'size="21" maxlength="20"', $auto_type ? false : true); ?></td>
     <td class="main">&nbsp;</td>
     <td class="main">&nbsp;</td>
     <td class="main" align="right"><?php echo TEXT_INACTIVE; ?></td>
     <td class="main"><?php echo html_checkbox_field('inactive', '1', $cInfo->inactive); ?></td>
    </tr>
    <tr>
      <td align="right" class="main"><?php echo GEN_FIRST_NAME; ?></td>
      <td class="main"><?php echo html_input_field('contact_first', $cInfo->contact_first, 'size="33" maxlength="32"', false); ?></td>
      <td align="right" class="main"><?php echo GEN_MIDDLE_NAME; ?></td>
      <td class="main"><?php echo html_input_field('contact_middle', $cInfo->contact_middle, 'size="33" maxlength="32"', false); ?></td>
      <td align="right" class="main"><?php echo GEN_LAST_NAME; ?></td>
      <td class="main"><?php echo html_input_field('contact_last', $cInfo->contact_last, 'size="33" maxlength="32"', false); ?></td>
    </tr>
	<tr>
	  <td class="main"><?php echo constant('ACT_' . strtoupper($type) . '_GL_ACCOUNT_TYPE'); ?></td>
	  <td class="main">
	  <?php
		$default_selection = ($action == 'new' ? AP_DEFAULT_INVENTORY_ACCOUNT : $cInfo->gl_type_account);
		$selection_array = gen_coa_pull_down();
		echo html_pull_down_menu('gl_type_account', $selection_array, $default_selection);
		?>
	  </td>
	  <td align="right" class="main"><?php echo constant('ACT_' . strtoupper($type) . '_ACCOUNT_NUMBER'); ?></td>
	  <td class="main"><?php echo html_input_field('account_number', $cInfo->account_number, 'size="17" maxlength="16"'); ?></td>
     <td class="main">&nbsp;</td>
     <td class="main">&nbsp;</td>
	</tr>
	<tr>
	  <td align="right" class="main"><?php echo constant('ACT_' . strtoupper($type) . '_REP_ID'); ?></td>
	  <td class="main">
	  <?php
		$default_selection = ($action == 'new' ? $_SESSION['admin_id'] : $cInfo->dept_rep_id);
		$selection_array = gen_get_rep_ids('v');
		echo html_pull_down_menu('dept_rep_id', $selection_array, $default_selection);
	  ?>
	  </td>
	  <td align="right" class="main"><?php echo constant('ACT_' . strtoupper($type) . '_ID_NUMBER'); ?></td>
	  <td class="main"><?php echo html_input_field('gov_id_number', $cInfo->gov_id_number, 'size="17" maxlength="16"'); ?></td>
	  <td align="right" class="main"><?php echo INV_ENTRY_ITEM_TAXABLE; ?></td>
	  <td class="main"><?php echo html_pull_down_menu('tax_id', $tax_rates, $cInfo->tax_id); ?></td>
	</tr>
  </table>
  </fieldset>

<?php // *********************** Mailing/Main Address (only one allowed) ****************************** ?>
  <fieldset class="formAreaTitle">
    <legend><?php echo ACT_CATEGORY_M_ADDRESS; ?></legend>
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <?php 
	    $var_name = $type . 'm_address';
		$temp_array = $cInfo->$var_name;
		$tmp = array();
		if (is_array($temp_array)) foreach ($temp_array[0] as $key => $value) {
		  $tmp[$type . 'm_' . $key] = $value;
		}
		$aInfo = new objectInfo($tmp);
	  	echo draw_address_fields($aInfo, $type . 'm', false, false, false); 
	  ?>
    </table>
  </fieldset>

<?php // *********************** PAYMENT TERMS  *************************************
  $terms = explode(':', $cInfo->special_terms);
?>
  <fieldset class="formAreaTitle">
    <legend><?php echo ACT_CATEGORY_PAYMENT_TERMS; ?></legend>
    <table border="0" width="100%" cellspacing="0" cellpadding="1">
      <tr>
		<td class="main">
<?php
echo html_radio_field('special_terms', 0, (($terms[0]=='0' || $terms[0]=='') ? true : false), '', 'onclick="changeOptions()"') . ACT_TERMS_USE_DEFAULTS . '<br />' . chr(10);
echo html_radio_field('special_terms', 1, ($terms[0]=='1' ? true : false), '', 'onclick="changeOptions()"') . ACT_COD_SHORT . '<br />' . chr(10);
echo html_radio_field('special_terms', 2, ($terms[0]=='2' ? true : false), '', 'onclick="changeOptions()"') . ACT_PREPAID . '<br />' . chr(10);
echo html_radio_field('special_terms', 3, ($terms[0]=='3' ? true : false), '', 'onclick="changeOptions()"') . ACT_SPECIAL_TERMS . '<br />' . chr(10);
echo html_radio_field('special_terms', 4, ($terms[0]=='4' ? true : false), '', 'onclick="changeOptions()"') . ACT_DAY_NEXT_MONTH . '<br />' . chr(10);
echo html_radio_field('special_terms', 5, ($terms[0]=='5' ? true : false), '', 'onclick="changeOptions()"') . ACT_END_OF_MONTH . chr(10);
?>
		</td>
		<td class="main" valign="top">
<?php
// terms[3] may contain either the net days or a date, set the defaults
if ($terms[0] == '4' || $terms[0] == '5') {
	$net_terms = constant($terms_type . '_NUM_DAYS_DUE');
	$date_terms = $terms[3];
} else {
	$net_terms = (($terms[3] <> '') ? $terms[3] : constant($terms_type . '_NUM_DAYS_DUE'));
	$date_terms = '';
}
echo TEXT_CURRENT . ' - ' . gen_terms_to_language($cInfo->special_terms, false, $terms_type) . '<br />' . chr(10);
echo ACT_DISCOUNT . html_input_field('early_percent', (($terms[1] <> '') ? $terms[1] : constant($terms_type . '_PREPAYMENT_DISCOUNT_PERCENT')), 'size="4"') . ACT_EARLY_DISCOUNT . '<br />' . chr(10);
echo ACT_DUE_IN . html_input_field('early_days', (($terms[2] <> '') ? $terms[2] : constant($terms_type . '_PREPAYMENT_DISCOUNT_DAYS')), 'size="3"') . ACT_TERMS_EARLY_DAYS . '<br />' . chr(10);
echo ACT_TERMS_NET . html_input_field('standard_days', $net_terms, 'size="3"') . ACT_TERMS_STANDARD_DAYS . '<br />' . chr(10);
echo html_calendar_field($cal_v_gen);
if ($terms[0] == '4' || $terms[0] == '5') {
	echo '<script type="text/javascript">';
	echo "document.contacts.elements['due_date'].value = '" . $date_terms . "';";
	echo '</script>';
}
echo '<br />' . ACT_TERMS_CREDIT_LIMIT . html_input_field('credit_limit', $currencies->format($terms[4] ? $terms[4] : AR_CREDIT_LIMIT_AMOUNT), 'size="10" style="text-align:right"') . '&nbsp;' . chr(10);
if (ENABLE_MULTI_CURRENCY) echo (' (' . DEFAULT_CURRENCY . ')' . chr(10)); 
?>
		</td>
	  </tr>
	</table>
  </fieldset>
</div>