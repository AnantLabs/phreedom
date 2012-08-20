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
//  Path: /modules/phreepos/pages/closing/template_main.php
//
 
// start the form
echo html_form('closingpos', FILENAME_DEFAULT, gen_get_all_get_params(array('action'))) . chr(10);
// include hidden fields
echo html_hidden_field('todo', '') . chr(10);
echo html_hidden_field('bill_acct_id',    $order->bill_acct_id) . chr(10);	// id of the account in the bill to/remit to
echo html_hidden_field('id',              $order->id) . chr(10);	// db journal entry id, null = new entry; not null = edit
echo html_hidden_field('bill_address_id', $order->bill_address_id) . chr(10);
echo html_hidden_field('bill_telephone1', $order->bill_telephone1) . chr(10);
echo html_hidden_field('bill_email',      $order->bill_email) . chr(10);
echo html_hidden_field('gl_disc_acct_id', '') . chr(10);

// customize the toolbar actions
$toolbar->icon_list['cancel']['params'] = 'onclick="location.href = \'' . html_href_link(FILENAME_DEFAULT, '', 'SSL') . '\'"';
$toolbar->icon_list['open']['show']     = false;
$toolbar->icon_list['delete']['show']   = false;
$toolbar->icon_list['save']['params']   = 'onclick="submitToDo(\'save\')"';
if ($security_level < 2) $toolbar->icon_list['save']['show'] = false;
$toolbar->icon_list['print']['show']    = false;
// pull in extra toolbar overrides and additions
if (count($extra_toolbar_buttons) > 0) {
  foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
}
// add the help file index and build the toolbar
$toolbar->add_help('??.??'); //need help here
echo $toolbar->build_toolbar();
if ($fields == null){ 
?>
<fieldset id="search_part">
	<ol>
<?php if ($tills->showDropDown()) {	// show currency slection pulldown 
		echo '<li><label>' . TEXT_TILL . ' ' . html_pull_down_menu('till_id', $tills->till_array(true) , 0, 'onchange="submitToDo(\'till_change\')"') . '</label></li>'; 
      }else{ 
		echo html_hidden_field('till_id', $tills->default_till()); 
	  }?> 
	</ol>
</fieldset>
<?php }else{?>
<table id="payment_table" class="ui-widget" style="border-collapse:collapse;">
<caption> <?php echo $tills->description ?></caption>
	<thead class="ui-widget-header">
		<tr>
			<th class="dataTableHeadingContent"></th>
			<th class="dataTableHeadingContent"><?php echo TEXT_PAYMENT_METHOD; ?></th>
			<th class="dataTableHeadingContent"><?php echo TEXT_CURRENCY; ?></th>
			<th class="dataTableHeadingContent"><?php echo TEXT_AMOUNT; ?></th>
			<th class="dataTableHeadingContent"></th>
		</tr>
   	</thead>
	<tbody id="payment_table_body">
	<?php
	foreach ($fields as $field){
		echo '<tr>';
			echo '<td>';
				echo $field['Opening'];
		 	echo '</td>';
		 	echo '<td>';
				echo $field['PaymentMethod'];
		 	echo '</td>';
		 	echo '<td>';
				echo $field['Currency'];
		 	echo '</td>';
		 	echo '<td>';
		 		//$currencies->format_full($query_result->fields['total_amount'], true, $query_result->fields['currencies_code'], $query_result->fields['currencies_value'])
				echo $field['AmountShouldBe'];//nog in valuta??
		 	echo '</td>';
		 	echo '<td>';
				echo html_input_field('amount_is', $field['AmountIs'], 'style="text-align:right" size="13" maxlength="20" onchange="updatetotal()"'); 
		 	echo '</td>';
		echo '</tr>';
	}
	?>
	</tbody>
	<tfoot class="ui-widget-header">
		<tr>
			<th class="dataTableHeadingContent"></th>
			<th class="dataTableHeadingContent"><?php echo TEXT_TOTAL; ?></th>
			<th class="dataTableHeadingContent"><?php echo html_input_field('total', $currencies->format($order->pmt_recvd), 'readonly="readonly" size="15" maxlength="20" style="text-align:right"'); ?></th>
			<th class="dataTableHeadingContent"></th>
			<th class="dataTableHeadingContent"></th>
		</tr>
		<tr>
			<th class="dataTableHeadingContent"></th>
			<th class="dataTableHeadingContent"><?php echo TEXT_BALANCE_DUE; ?></th>
			<th class="dataTableHeadingContent"><?php echo html_input_field('bal_due', $currencies->format($order->bal_due), 'readonly="readonly" size="15" maxlength="20" style="text-align:right"'); ?></th>
			<th class="dataTableHeadingContent"></th>
			<th class="dataTableHeadingContent"></th>
		</tr>
	</tfoot>
</table>
<?php }?>
</form>


