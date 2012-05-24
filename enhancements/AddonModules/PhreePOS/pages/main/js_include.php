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
//  Path: /modules/phreepos/pages/main/js_include.php
//
$js_tax_rates = 'var tax_rates = new Array(' . count($tax_rates) . ');' . chr(10);
for ($i = 0; $i < count($tax_rates); $i++) {
  $js_tax_rates .= 'tax_rates[' . $i . '] = new salesTaxes("' . $tax_rates[$i]['id'] . '", "' . $tax_rates[$i]['text'] . '", "' . $tax_rates[$i]['rate'] . '");' . chr(10);
};

?>
<script type="text/javascript">
<!--
<?php echo $js_tax_rates;?>
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
var setId                = 1; // flag used for AJAX loading of sku for bar code reading of line item
var skuLength            = <?php echo ORD_BAR_CODE_LENGTH; ?>;
var resClockID           = 0;
var default_disc_acct    = <?Php echo AR_DISCOUNT_SALES_ACCOUNT;?>;
var max_sku_len          = <?php echo MAX_INVENTORY_SKU_LENGTH; ?>;
var auto_load_sku        = <?php echo INVENTORY_AUTO_FILL; ?>;
var image_ser_num        = '<?php echo TEXT_SERIAL_NUMBER; ?>';
var add_array            = new Array("<?php echo implode('", "', $js_arrays['fields']); ?>");
var default_array        = new Array("<?php echo implode('", "', $js_arrays['text']); ?>");
var journalID            = '<?php echo JOURNAL_ID; ?>';
var securityLevel        = <?php echo $security_level; ?>;
var account_type         = '<?php echo $account_type; ?>';
var text_search          = '<?php ?>';
var text_enter_new       = '<?php echo TEXT_ENTER_NEW; ?>';
var text_properties      = '<?php echo TEXT_PROPERTIES; ?>';
var post_error           = <?php echo $error ? "true" : "false"; ?>;
var default_sales_tax    = '-1';
var image_delete_text    = '<?php echo TEXT_DELETE; ?>';
var image_delete_msg     = '<?php echo TEXT_DELETE_ENTRY; ?>';
var store_country_code   = '<?php echo COMPANY_COUNTRY; ?>';
var delete_icon_HTML     = '<?php echo substr(html_icon("emblems/emblem-unreadable.png", TEXT_DELETE, "small", "onclick=\"if (confirm(\'" . TEXT_DELETE_ENTRY . "\')) removeInvRow("), 0, -2); ?>';
var delete_icon_HTML_PMT = '<?php echo substr(html_icon("emblems/emblem-unreadable.png", TEXT_DELETE, "small", "onclick=\"if (confirm(\'" . TEXT_DELETE_ENTRY . "\')) removePmtRow("), 0, -2); ?>';
var serial_num_prompt    = '<?php echo ORD_JS_SERIAL_NUM_PROMPT; ?>';
var show_status          = '<?php echo ($account_type == "v") ? AP_SHOW_CONTACT_STATUS : AR_SHOW_CONTACT_STATUS; ?>';
var warn_form_modified   = '<?php echo ORD_WARN_FORM_MODIFIED; ?>';
var default_inv_acct     = '<?php echo DEF_INV_GL_ACCT; ?>';
var defaultCurrency      = '<?php echo DEFAULT_CURRENCY; ?>';
var tax_before_discount  = '<?php echo ($account_type == "c") ? AR_TAX_BEFORE_DISCOUNT : AP_TAX_BEFORE_DISCOUNT; ?>';
var add_array			 = new Array("<?php echo implode('", "', $js_arrays['fields']); ?>");
var default_array        = new Array("<?php echo implode('", "', $js_arrays['text']); ?>");
var bill_add             = new Array();
var save_allowed		 = true;
var display_with_tax     = <?php echo PHREEPOS_DISPLAY_WITH_TAX; ?>;
var discount_from_total  = <?php echo PHREEPOS_DISCOUNT_OF; ?>;
var rounding_of          = <?php echo PHREEPOS_ROUNDING; ?>;
// List the currency codes and exchange rates
<?php if (ENABLE_MULTI_CURRENCY) echo $currencies->build_js_currency_arrays(); ?>
// List the tax rates
<?php echo $js_tax_rates; ?>
<?php echo $js_pmt_types; ?>

function init() {
  document.getElementById('bill_to_select').style.visibility = 'hidden';
  setField('search', text_search);
  document.getElementById('disc_gl_acct_id').value    = default_disc_acct;
  // change color of the bill address fields if they are the default values
  var add_id;
  for (var i=0; i<add_array.length; i++) {
	add_id = add_array[i];
	if (document.getElementById('bill_'+add_id).value == '') {
	  document.getElementById('bill_'+add_id).value = default_array[i];
	}
	if (document.getElementById('bill_'+add_id).value == default_array[i]) {
	  if (add_id != 'country_code') document.getElementById('bill_'+add_id).style.color = inactive_text_color;
	}
  }
  document.getElementById('sku').focus();
  refreshOrderClock(); 
}

function check_form() {
  var error = 0;
  var i, stock, qty, inactive, message;
  var error_message = "<?php echo JS_ERROR; ?>";
  var todo    = document.getElementById('todo').value;
  if (error == 1) {
    alert(error_message);
    return false;
  }
  return true;
}

// Insert other page specific functions here.
function refreshOrderClock() {
  if (resClockID) {
    clearTimeout(resClockID);
    resClockID = 0;
  }
  if (setId) { // call the ajax to load the inventory info
    var upc = document.getElementById('sku').value;
    if (upc != text_search && upc.length == skuLength) {
      var acct = document.getElementById('bill_acct_id').value;
	  var qty  = 1;
	  $.ajax({
		type: "GET",
		contentType: "application/json; charset=utf-8",
		url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuDetails&cID='+acct+'&qty='+qty+'&upc='+upc+'&rID='+setId+'&jID='+journalID,
		dataType: ($.browser.msie) ? "text" : "xml",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		  alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
		},
		success: fillInventory
	  });
	  document.getElementById('sku').value = '';
	}
  }
  resClockID = setTimeout("refreshOrderClock()", 250);
}

function salesTaxes(id, text, rate) {
  this.id   = id;
  this.text = text;
  this.rate = rate;
}

function ClearForm() {	
}

function resetForm() {
	clearAddress('bill');
    document.getElementById('sku').value                = text_search;
    document.getElementById('sku').style.color          = inactive_text_color;
    document.getElementById('purchase_invoice_id').value= '';
	document.getElementById('id').value                 = '';
    document.getElementById('printed').value            = '0';
	document.getElementById('store_id').value           = '';
	document.getElementById('rep_id').value             = '';
	document.getElementById('subtotal').value           = formatted_zero;
	document.getElementById('disc_percent').value       = formatted_zero;
	document.getElementById('discount').value           = formatted_zero;
	document.getElementById('sales_tax').value          = formatted_zero;
	document.getElementById('rounded_of').value         = formatted_zero;
	document.getElementById('total').value              = formatted_zero;
	document.getElementById('pmt_recvd').value          = formatted_zero;
	document.getElementById('bal_due').value            = formatted_zero;
	document.getElementById('display_currency').value   = defaultCurrency;
	document.getElementById('currencies_code').value    = defaultCurrency;
	document.getElementById('currencies_value').value   = '1';
	document.getElementById('disc_gl_acct_id').value    = default_disc_acct;
	$('#inv_popup > img').attr('src', '');
	$('#curr_image').attr('src', '');
	// handle checkboxes
	document.getElementById('bill_add_update').checked  = false;
// remove all item rows and add a new blank one
	while (document.getElementById('item_table').rows.length > 1) document.getElementById('item_table').deleteRow(-1);
	addInvRow();
	while (document.getElementById('pmt_table').rows.length > 1) document.getElementById('pmt_table').deleteRow(-1);
	addPmtRow();
	document.getElementById('sku').focus();
}

function clearAddress(type) {
  for (var i=0; i<add_array.length; i++) {
	var add_id = add_array[i];
	document.getElementById(type+'_acct_id').value      = '';
	document.getElementById(type+'_address_id').value   = '';
	document.getElementById('search').value   			= '';
	document.getElementById(type+'_country_code').value = store_country_code;
	if (add_id != 'country_code') document.getElementById(type+'_'+add_id).style.color = inactive_text_color;
	document.getElementById(type+'_'+add_id).value = default_array[i];
  	document.getElementById(type+'_to_select').style.visibility = 'hidden';
  	if (document.getElementById(type+'_to_select')) {
      while (document.getElementById(type+'_to_select').options.length) {
	    document.getElementById(type+'_to_select').remove(0);
      }
  	}
  }
}

function ajaxOrderData(cID, oID, jID, open_order, ship_only) {
  $.ajax({
    type: "GET",
    contentType: "application/json; charset=utf-8",
    url: 'index.php?module=phreebooks&page=ajax&op=load_order&cID='+cID+'&oID='+oID+'&jID='+jID+'&so_po=0&ship_only=0',
    dataType: ($.browser.msie) ? "text" : "xml",
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
    },
	success: fillOrderData
  });
}

function fillOrderData(sXml) { // edit response form fill
  var xml = parseXml(sXml);
  if (!xml) return;
  if ($(xml).find("OrderData").length) {
	orderFillAddress(xml, 'bill', false);
	fillOrder(xml);
  } else if ($(xml).find("BillContact").length) {
    orderFillAddress(xml, 'bill', true);
  }
}

function orderFillAddress(xml, type, fill_address) {
  var newOpt, mainType;
  while (document.getElementById(type+'_to_select').options.length) document.getElementById(type+'_to_select').remove(0);
  var cTag = 'BillContact';
  $(xml).find(cTag).each(function() {
    var id = $(this).find("id").text();
	if (!id) return;
    mainType = $(this).find("type").first().text() + 'm';
    switch (type) {
	  default:
      case 'bill':
		bill_add          = this;
		default_sales_tax = $(this).find("tax_id").text();
		default_inv_acct  = ($(this).find("gl_type_account").text()) ? $(this).find("gl_type_account").text() : '';
		insertValue('bill_acct_id',    id);
		insertValue('search',          $(this).find("short_name").text());
		insertValue('acct_1',          default_inv_acct);
	//	insertValue('rep_id',          $(this).find("dept_rep_id").text());
		var rowCnt = 1;
		while(true) {
		  if (!document.getElementById('tax_'+rowCnt)) break;
		  document.getElementById('tax_'+rowCnt).value = $(this).find("tax_id").text();
		  rowCnt++;
		}
		if (show_status == '1') {
		  window.open("index.php?module=phreebooks&page=popup_status&id="+id,"contact_status","width=500px,height=300px,resizable=0,scrollbars=1,top=150,left=200");
		}
		break;
    }
	//now fill the addresses
    var iIndex = 0;
    $(this).find("Address").each(function() {
      newOpt = document.createElement("option");
	  newOpt.text = $(this).find("primary_name").text() + ', ' + $(this).find("city_town").text() + ', ' + $(this).find("postal_code").text();
	  document.getElementById(type+'_to_select').options.add(newOpt);
	  document.getElementById(type+'_to_select').options[iIndex].value = $(this).find("address_id").text();
      if (fill_address && $(this).find("type").text() == mainType) { // also fill the fields
	    insertValue(type+'_address_id', $(this).find("address_id").text());
	    $(this).children().each (function() {
		  var tagName = this.tagName;
		  if (document.getElementById(type+'_'+tagName)) {
		    document.getElementById(type+'_'+tagName).value = $(this).text();
		    document.getElementById(type+'_'+tagName).style.color = '';
		  }
	    });
	  }
	  iIndex++;
    });
    // add a option for creating a new address
    newOpt = document.createElement("option");
    newOpt.text = text_enter_new;
    document.getElementById(type+'_to_select').options.add(newOpt);	
    document.getElementById(type+'_to_select').options[iIndex].value = '0';
    document.getElementById(type+'_to_select').style.visibility      = 'visible';
    document.getElementById(type+'_to_select').disabled              = false;
  });
  numRows = document.getElementById('item_table').rows.length;
  for (i=1; i<numRows; i++) {
	if(document.getElementById('sku_'+i).value !=''){
  	  updateRowTotal(i, true);
	}
  }
}

function fillOrder(xml) {
  $(xml).find("OrderData").each(function() {
	$(this).children().each (function() {
	  var tagName = this.tagName;
	  if (document.getElementById(tagName)) {
	    document.getElementById(tagName).value = $(this).first().text();
	    document.getElementById(tagName).style.color = '';
	  }
	});
    // fix some special cases, checkboxes, and active fields
    document.getElementById('display_currency').value = $(this).find("currencies_code").text();
    // disable the purchase_invoice_id field since it cannot change, except purchase/receive
    if ($(this).find("id").first().text() && journalID != '6' && journalID != '7' && journalID != '21') {
	  document.getElementById('purchase_invoice_id').readOnly = true;
    }
    if ($(this).find("id").first().text() && securityLevel < 3) { // turn off some icons
//	  removeElement('tb_main_0', 'tb_icon_print');
//	  removeElement('tb_main_0', 'tb_icon_save');
    }
    // fill inventory rows and add a new blank one
    var order_discount = formatted_zero;
    var jIndex = 1;
    $(this).find("Item").each(function() {
	  var gl_type = $(this).find("gl_type").text();
      switch (gl_type) {
	    case 'ttl':
	    case 'tax': // the total and tax will be recalculated when the form is loaded
	      break;
	    case 'dsc':
	      order_discount =                   $(this).find("total").text();
		  if ($(this).find("gl_account").text()) insertValue('disc_gl_acct_id', $(this).find("gl_account").text());
		  break;
	    case 'soo':
	    case 'sos':
	    case 'poo':
	    case 'por':
		  insertValue('id_'  + jIndex,       $(this).find("id").text());
		  insertValue('pstd_' + jIndex,      $(this).find("pstd").text());
		  insertValue('sku_'  + jIndex,      $(this).find("sku").text());
		  insertValue('desc_'  + jIndex,     $(this).find("description").text());
		  insertValue('acct_'  + jIndex,     $(this).find("gl_account").text());
		  insertValue('tax_'  + jIndex,      $(this).find("taxable").text());
		  insertValue('full_'  + jIndex,     $(this).find("full_price").text());
		  insertValue('serial_'  + jIndex,   $(this).find("serialize").text());
		  insertValue('inactive_'  + jIndex, $(this).find("inactive").text());
		  insertValue('price_' + jIndex,     $(this).find("unit_price").text());
		  insertValue('total_' + jIndex,     $(this).find("total").text());
		  updateRowTotal(jIndex, false);
		  addInvRow();
		  jIndex++;
	    default: // do nothing
	  }
    });
    insertValue('discount', order_discount);
    calculateDiscountPercent();
  });
}

function accountGuess(force) {
  if (force) {
	AccountList();
	return;
  } 
  if (post_error) return; // leave the data there, since form was reloaded with failed post data
  if (document.getElementById('id').value) return; // if there's an id, it's an edit, return
  var warn = true;
  var guess = document.getElementById('search').value;
  // test for data already in the form
  if (guess != text_search && guess != '') {
    if (document.getElementById('bill_acct_id').value ||
        document.getElementById('bill_primary_name').value != default_array[0]) {
          warn = confirm(warn_form_modified);
	}
	if (warn) {
	  $.ajax({
		type: "GET",
		contentType: "application/json; charset=utf-8",
		url: 'index.php?module=phreepos&page=ajax&op=load_searches&jID='+journalID+'&type='+account_type+'&guess='+guess+'&jID='+journalID,
		dataType: ($.browser.msie) ? "text" : "xml",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		  alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
		},
		success: processGuess
	  });
    }
  }
}

function processGuess(sXml) {
  var xml = parseXml(sXml);
  if (!xml) return;
  if ($(xml).find("result").text() == 'success') {
    fillOrderData(xml);
  } else {
	AccountList();
  }
}

function AccountList(currObj) {
  window.open("index.php?module=contacts&page=popup_accts&type="+account_type+"&form=orders&fill=bill&jID=19&search_text="+document.getElementById('search').value,"accounts","width=850px,height=550px,resizable=1,scrollbars=1,top=150,left=100");
}

function InventoryList(rowCnt) {
	var storeID = document.getElementById('store_id').value;
	var sku     = document.getElementById('sku').value;
	var cID     = document.getElementById('bill_acct_id').value;
	window.open("index.php?module=inventory&page=popup_inv&type="+account_type+"&rowID="+rowCnt+"&storeID="+storeID+"&cID="+cID+"&search_text="+sku,"inventory","width=700px,height=550px,resizable=1,scrollbars=1,top=150,left=200");
}

function serialList(rowID) {
   var choice    = document.getElementById(rowID).value;
   var newChoice = prompt(serial_num_prompt, choice);
   if (newChoice) document.getElementById(rowID).value = newChoice;
}

function fillAddress(type) {
  var index = document.getElementById(type+'_to_select').value;
  var address;
  if (type == "bill") address = bill_add;
  if (index == '0') { // set to defaults
    document.getElementById(type+'_acct_id').value    = 0;
    document.getElementById(type+'_address_id').value = 0;
    for (var i=0; i<add_array.length; i++) {
	  add_id = add_array[i];
	  if (add_id != 'country_code') document.getElementById(type+'_'+add_id).style.color = inactive_text_color;
	  document.getElementById(type+'_'+add_id).value = default_array[i];
    }
    return;
  }
  $(address).find("Address").each(function() {
    if ($(this).find("address_id").text() == index) {
      document.getElementById(type+'_acct_id').value    = $(this).find("ref_id").text();
      document.getElementById(type+'_address_id').value = (index == 'new') ? '0' : $(this).find("address_id").text();
      var add_id;
      for (var i=0; i<add_array.length; i++) {
	    add_id = add_array[i];
	    if (index != '0' && $(this).find(add_id).text()) {
	      document.getElementById(type+'_'+add_id).style.color = '';
	      document.getElementById(type+'_'+add_id).value = $(this).find(add_id).text();
	    } else {
	      if (add_id != 'country_code') document.getElementById(type+'_'+add_id).style.color = inactive_text_color;
	      document.getElementById(type+'_'+add_id).value = default_array[i];
	    }
      }
	}
  });
}

function addInvRow() {
  var newCell;
  var cell;
  var newRow = document.getElementById('item_table').insertRow(-1);
  var rowCnt = newRow.rowIndex;
  // NOTE: any change here also need to be made to template form for reload if action fails
  cell  = '<td align="center">';
  cell += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', image_delete_text, 'onclick="if (confirm(\''+image_delete_msg+'\')) removeInvRow('+rowCnt+');"') + '</td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell  = '<td align="left"><input type="text" name="pstd_'+rowCnt+'" id="pstd_'+rowCnt+'" size="7" maxlength="6" onchange="updateRowTotal('+rowCnt+', true)" style="text-align:right" />';
  cell += '&nbsp;' + buildIcon(icon_path+'16x16/actions/tab-new.png', image_ser_num, 'onclick="serialList(\'serial_'+rowCnt+'\')"');
  cell += '</td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell  = '<td align="left"><input type="text" name="sku_'+rowCnt+'" id="sku_'+rowCnt+'" readonly="readonly" size="'+(max_sku_len+1)+'" maxlength="'+max_sku_len+'"  />&nbsp;';
  cell += buildIcon(icon_path+'16x16/actions/document-properties.png', text_properties, 'id="sku_prop_'+rowCnt+'" align="top" style="cursor:pointer" onclick="InventoryProp('+rowCnt+')"');
  cell += '</td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell = '<td><input name="desc_'+rowCnt+'" id="desc_'+rowCnt+'" readonly="readonly" size="50" maxlength="255" /></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  if (display_with_tax) { 
    cell  = '<td align="center"><input type="text" name="wtprice_'+rowCnt+'" id="wtprice_'+rowCnt+'" <?php if($security_level < 3) echo 'readonly="readonly"'; ?> size="10" maxlength="15" style="text-align:right" onchange="rowWithTax('+rowCnt+')" /></td>';
  }else{
  	cell  = '<td align="center"><input type="text" name="price_'+rowCnt+'"   id="price_'+rowCnt+'"   <?php if($security_level < 3) echo 'readonly="readonly"'; ?> size="10" maxlength="15" style="text-align:right" onchange="updateRowTotal('+rowCnt+',false)" /></td>';
  }
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell  = '<td align="center">';
// Hidden fields
  cell += '<input type="hidden" name="id_'+rowCnt+'" id="id_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="stock_'+rowCnt+'" id="stock_'+rowCnt+'" value="NA" />';
  cell += '<input type="hidden" name="inactive_'+rowCnt+'" id="inactive_'+rowCnt+'" value="0" />';
  cell += '<input type="hidden" name="serial_'+rowCnt+'" id="serial_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="full_'+rowCnt+'" id="full_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="disc_'+rowCnt+'" id="disc_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="acct_'+rowCnt+'" id="acct_'+rowCnt+'" value="'+default_inv_acct+'" />';
  cell += '<input type="hidden" name="tax_'+rowCnt+'" id="tax_'+rowCnt+'" value="" />';
  if (display_with_tax) { 
	cell += '<input type="hidden" name="price_'+rowCnt+'" id="price_'+rowCnt+'" value="'+formatted_zero+'" />';
    cell += '<input type="hidden" name="total_'+rowCnt+'" id="total_'+rowCnt+'" value="'+formatted_zero+'" />';
    cell += '<input type="text" name="wttotal_'+rowCnt+'" id="wttotal_'+rowCnt+'" value="'+formatted_zero+'" readonly="readonly" size="11" maxlength="20" style="text-align:right" /></td>';
  }else{
	cell += '<input type="hidden" name="wtprice_'+rowCnt+'" id="wtprice_'+rowCnt+'" value="'+formatted_zero+'" />';
    cell += '<input type="hidden" name="wttotal_'+rowCnt+'" id="wttotal_'+rowCnt+'" value="'+formatted_zero+'" />';
    cell += '<input type="text" name="total_'+rowCnt+'" id="total_'+rowCnt+'"   value="'+formatted_zero+'" readonly="readonly" size="11" maxlength="20" style="text-align:right" /></td>';
  }
// End hidden fields
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  return rowCnt;
}

function removeInvRow(index) {
  var i, acctIndex, offset, newOffset;
  var firstRow = false;
  var numRows = document.getElementById('item_table').rows.length - 1;
  if (numRows == 1) firstRow = true;
  // remove row from display by reindexing and then deleting last row
  for (i=index; i<numRows; i++) {
	// move the delete icon from the previous row
	offset    = i+1;
	newOffset = i;
	document.getElementById('item_table').rows[newOffset].cells[0].innerHTML = delete_icon_HTML + i + ');">';
	document.getElementById('pstd_'+i).value     = document.getElementById('pstd_'+(i+1)).value;
	document.getElementById('sku_'+i).value      = document.getElementById('sku_'+(i+1)).value;
	document.getElementById('desc_'+i).value     = document.getElementById('desc_'+(i+1)).value;
	document.getElementById('price_'+i).value    = document.getElementById('price_'+(i+1)).value;
	document.getElementById('acct_'+i).value     = document.getElementById('acct_'+(i+1)).value;
	document.getElementById('tax_'+i).value      = document.getElementById('tax_'+(i+1)).value;
// Hidden fields
	document.getElementById('id_'+i).value       = document.getElementById('id_'+(i+1)).value;
	document.getElementById('stock_'+i).value    = document.getElementById('stock_'+(i+1)).value;
	document.getElementById('inactive_'+i).value = document.getElementById('inactive_'+(i+1)).value;
	document.getElementById('serial_'+i).value   = document.getElementById('serial_'+(i+1)).value;
	document.getElementById('full_'+i).value     = document.getElementById('full_'+(i+1)).value;
	document.getElementById('disc_'+i).value     = document.getElementById('disc_'+(i+1)).value;
// End hidden fields
	document.getElementById('total_'+i).value    = document.getElementById('total_'+(i+1)).value;
	document.getElementById('wttotal_'+i).value  = document.getElementById('wttotal_'+(i+1)).value;
	document.getElementById('wtprice_'+i).value  = document.getElementById('wtprice_'+(i+1)).value;
  }
  document.getElementById('item_table').deleteRow(-1);
  updateTotalPrices();
  if (firstRow) addInvRow();
} 

function addPmtRow() {
  var newCell;
  var cell;
  var newRow = document.getElementById('pmt_table').insertRow(-1);
  var rowCnt = newRow.rowIndex;
  // NOTE: any change here also need to be made to template form for reload if action fails
  cell  = '<td align="left">';
  cell += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', image_delete_text, 'onclick="if (confirm(\''+image_delete_msg+'\')) removePmtRow('+rowCnt+');"') + '</td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell = '<td class="main"><input name="pdes_'+rowCnt+'" id="pdes_'+rowCnt+'" readonly="readonly" size="20" maxlength="25" /></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell  = '<td class="main" align="center">';
// Hidden fields
  cell += '<input type="hidden" name="meth_'+rowCnt+'" id="meth_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="f0_'+rowCnt+'" id="f0_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="f1_'+rowCnt+'" id="f1_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="f2_'+rowCnt+'" id="f2_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="f3_'+rowCnt+'" id="f3_'+rowCnt+'" value="" />';
  cell += '<input type="hidden" name="f4_'+rowCnt+'" id="f4_'+rowCnt+'" value="" />';
// End hidden fields
  cell += '<input type="text" name="pmt_'+rowCnt+'" id="pmt_'+rowCnt+'" value="'+formatted_zero+'" readonly="readonly" size="11" maxlength="20" style="text-align:right" /></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  return rowCnt;
}

function removePmtRow(index) {
  var i, acctIndex, offset, newOffset;
  var firstRow = false;
  var numRows = document.getElementById('pmt_table').rows.length - 1;
  if (numRows == 1) firstRow = true;
  // remove row from display by reindexing and then deleting last row
  for (i=index; i<numRows; i++) {
	// move the delete icon from the previous row
	offset    = i+1;
	newOffset = i;
	document.getElementById('pmt_table').rows[newOffset].cells[0].innerHTML = delete_icon_HTML_PMT + i + ');">';
	document.getElementById('pdes_'+i).value = document.getElementById('pdes_'+(i+1)).value;
// Hidden fields
	document.getElementById('meth_'+i).value = document.getElementById('meth_'+(i+1)).value;
	document.getElementById('f0_'+i).value   = document.getElementById('f0_'+(i+1)).value;
	document.getElementById('f1_'+i).value   = document.getElementById('f1_'+(i+1)).value;
	document.getElementById('f2_'+i).value   = document.getElementById('f2_'+(i+1)).value;
	document.getElementById('f3_'+i).value   = document.getElementById('f3_'+(i+1)).value;
	document.getElementById('f4_'+i).value   = document.getElementById('f4_'+(i+1)).value;
// End hidden fields
	document.getElementById('pmt_'+i).value  = document.getElementById('pmt_'+(i+1)).value;
  }
  document.getElementById('pmt_table').deleteRow(-1);
  if (firstRow) addPmtRow();
  updateTotalPrices();
} 

function rowWithTax(rowCnt){
	tax_index = document.getElementById('tax_'+rowCnt).value;
	var price = document.getElementById('wtprice_'+rowCnt).value;
	document.getElementById('wtprice_'+rowCnt).value = formatCurrency(cleanCurrency(price));
	text = formatCurrency(cleanCurrency(price )/ (1+(tax_rates[tax_index].rate / 100)));
	document.getElementById('price_'+rowCnt).value = text;
	updateRowTotal(rowCnt, false);
}

function updateRowTotal(rowCnt, useAjax) {
	var unit_price   = cleanCurrency(document.getElementById('price_'+rowCnt).value);
	var full_price   = cleanCurrency(document.getElementById('full_' +rowCnt).value);
	var tax_index    = document.getElementById('tax_'+rowCnt).value;
	var wtunit_price = unit_price * (1 +(tax_rates[tax_index].rate / 100));
	var qty          = parseFloat(document.getElementById('pstd_'+rowCnt).value);
	if (isNaN(qty)) qty = 1; // if blank or a non-numeric value is in the pstd field, assume one
	var total_line   = qty * unit_price;
	var total_l      = new String(total_line);
	var wttotal_line = qty * wtunit_price;
	var wttotal_l    = new String(wttotal_line);
	document.getElementById('price_'   +rowCnt).value    = formatPrecise(unit_price);
	document.getElementById('total_'   +rowCnt).value    = formatCurrency(total_l);
	document.getElementById('wttotal_' +rowCnt).value    = formatCurrency(wttotal_l);
	document.getElementById('wtprice_' +rowCnt).value	 = formatCurrency(wtunit_price);
	// calculate discount
	if (full_price > 0) {
	  var discount = (full_price - unit_price) / full_price;
	  document.getElementById('disc_'+rowCnt).value = new String(Math.round(1000*discount)/10) + ' %';
	}
	updateTotalPrices();
	// call the ajax price sheet update based on customer
	if (useAjax && qty != 0 && sku != '' && sku != text_search) {
	  var sku = document.getElementById('sku_'+rowCnt).value;
	  var cID = document.getElementById('bill_acct_id').value;
	  if (auto_load_sku) {
	    $.ajax({
	      type: "GET",
	      contentType: "application/json; charset=utf-8",
	      url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuPrice&cID='+cID+'&sku='+sku+'&qty='+qty+'&rID='+rowCnt+'&strict=1',
	      dataType: ($.browser.msie) ? "text" : "xml",
	      error: function(XMLHttpRequest, textStatus, errorThrown) {
		    alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
	      },
	      success: processSkuPrice
	    });
	  }
	}
}

//ajax response to price sheet request
function processSkuPrice(sXml) { // call back function
  var xml = parseXml(sXml);
  if (!xml) return;
  var exchange_rate = document.getElementById('currencies_value').value;
  var rowCnt = $(xml).find("rID").text();
  if(formatPrecise($(xml).find("sales_price").text()) != formatted_zero ){
    document.getElementById('price_'   +rowCnt).value    = formatPrecise($(xml).find("sales_price").text() * exchange_rate);
    document.getElementById('full_'    +rowCnt).value    = formatCurrency($(xml).find("full_price").text() * exchange_rate);
    updateRowTotal(rowCnt, false);
  }
}

function updateUnitPrice(rowCnt) {
  var total_line = cleanCurrency(document.getElementById('total_'+rowCnt).value);
  document.getElementById('total_'+rowCnt).value = formatCurrency(total_line);
  var qty = parseFloat(document.getElementById('pstd_'+rowCnt).value);
  if (isNaN(qty)) {
	qty = 1;
	document.getElementById('pstd_'+rowCnt).value = qty;
  }
  var unit_price = total_line / qty;
  var unit_p = new String(unit_price);
  document.getElementById('price_'+rowCnt).value = formatPrecise(unit_p);
  updateTotalPrices();
}

function calculateRoundingOf(new_total){
  switch (rounding_of){
	case 0: // no rounding of.
		return new_total;
		break;
	case 1:// rounds of to the nearest Integer. in favor of the customer
		var result = Math.floor (new_total);
		var differance = result - (Math.round(new_total * 100) / 100);
		document.getElementById('rounded_of').value = formatCurrency(differance);
		return result;
		break;
		
	case 2:// rounds of to the nearest 10cents. in favor of the customer
		var result = Math.floor (new_total * 10)/10;
		var differance = result - (Math.round(new_total * 100) / 100);
		document.getElementById('rounded_of').value = formatCurrency(differance);
		return result;
		break;
	case 3:// rounds of to the nearest 0, 5 or 10 cents (1,2,6,7 go down 3,4,8,9 go up)
		var result = Math.round (new_total * 20)/20;
		var differance = result - (Math.round(new_total * 100) / 100);
		document.getElementById('rounded_of').value = formatCurrency(differance);
		return result;
		break;
		
	}
}

function updateTotalPrices() {
  var discount = parseFloat(cleanCurrency(document.getElementById('discount').value));
  if (isNaN(discount)) discount = 0;
  var discountPercent = parseFloat(cleanCurrency(document.getElementById('disc_percent').value));
  if (isNaN(discountPercent)) discountPercent = 0;
  var subtotal         = 0;
  var taxable_subtotal = 0;
  var lineTotal        = '';
  var numRows = document.getElementById('item_table').rows.length;
  for (var i=1; i<numRows; i++) {
    lineTotal  = parseFloat(cleanCurrency(document.getElementById('total_'+i).value));
  	if (document.getElementById('tax_'+i).value != '0') {
      tax_index = document.getElementById('tax_'+i).value;
	  if (tax_index == -1 || tax_index == '') { // if the rate array index is not defined
		tax_index = 0;
		document.getElementById('tax_'+i).value = tax_index;
	  }
	  if (tax_before_discount == '0') { // tax after discount
        taxable_subtotal += lineTotal * (1-(discountPercent/100)) * (tax_rates[tax_index].rate / 100);
	  } else { 
        taxable_subtotal += lineTotal * (tax_rates[tax_index].rate / 100);
	  }
	}
	subtotal += lineTotal;
  }
  // recalculate discount
  if (discount_from_total){
	discount        = (discountPercent/100) * (subtotal + taxable_subtotal) ;
  }else  {
    discount        = subtotal * (discountPercent/100);
  }
  var strDiscount = new String(discount);
  document.getElementById('discount').value = formatCurrency(strDiscount);
  var nst         = new String(taxable_subtotal);
  document.getElementById('sales_tax').value = formatCurrency(nst);
  var st          = new String(subtotal);
  document.getElementById('subtotal').value = formatCurrency(st);
  var new_total   = calculateRoundingOf(subtotal - discount + taxable_subtotal);
  var tot         = new String(new_total);
  document.getElementById('total').value = formatCurrency(tot);
  var numRows     = document.getElementById('pmt_table').rows.length;
  var pmtTotal    = 0;
  for (var i=1; i<numRows; i++) {
    pmtTotal += parseFloat(cleanCurrency(document.getElementById('pmt_'+i).value));
  }
  document.getElementById('pmt_recvd').value = formatCurrency(pmtTotal);
  var balDue = tot - pmtTotal;
  document.getElementById('bal_due').value = formatCurrency(balDue);
}

function calculateDiscountPercent() {
  var percent  = parseFloat(cleanCurrency(document.getElementById('disc_percent').value));
  if (discount_from_total){
    var Total = parseFloat(cleanCurrency(document.getElementById('total').value));
    var discount = new String((percent / 100) * Total);
  }else{
  	var subTotal = parseFloat(cleanCurrency(document.getElementById('subtotal').value));
  	var discount = new String((percent / 100) * subTotal);
  }
  document.getElementById('discount').value = formatCurrency(discount);
  updateTotalPrices();
}

function calculateDiscount() {
  // determine the discount percent
  var discount = parseFloat(cleanCurrency(document.getElementById('discount').value));
  if (isNaN(discount)) discount = 0;
  if (discount_from_total){
    var StartValue = parseFloat(cleanCurrency(document.getElementById('total').value));
  }else{
  	var StartValue = parseFloat(cleanCurrency(document.getElementById('subtotal').value));
  }
  if (StartValue != 0) {
    var percent = 100000 * (1 - ((StartValue - discount) / StartValue));
    document.getElementById('disc_percent').value = formatCurrency(Math.round(percent) / 1000);
  } else {
  	document.getElementById('disc_percent').value = '0.00';
  }
  updateTotalPrices();
}

function recalculateCurrencies() {
  var workingTotal, workingUnitValue, itemTotal, newTotal;
  var currentCurrency = document.getElementById('currencies_code').value;
  var currentValue = parseFloat(document.getElementById('currencies_value').value);
  var desiredCurrency = document.getElementById('display_currency').value;
  for (var i=0; i<js_currency_codes.length; i++) {
	if (js_currency_codes[i] == desiredCurrency) var newValue = js_currency_values[i];
  }
  // update the line item table
  var numRows = document.getElementById('item_table').rows.length;
  for (var i=1; i<numRows; i++) {
	itemTotal = parseFloat(cleanCurrency(document.getElementById('total_'+i).value, currentCurrency));
	var tax_index  = document.getElementById('tax_'+i).value;
	if (isNaN(itemTotal)) continue;
	workingTotal = itemTotal / currentValue;
    newTotal = workingTotal * newValue;
	workingUnitValue = newTotal / document.getElementById('pstd_'+i).value;
	if (isNaN(workingUnitValue)) continue;
	document.getElementById('total_'   +i).value    = formatCurrency(new String(newTotal), desiredCurrency);
	document.getElementById('price_'   +i).value    = formatPrecise(new String(workingUnitValue), desiredCurrency);
	updateRowTotal(i, false);
	document.getElementById('wttotal_' +i).value    = formatCurrency(newTotal * (1 +(tax_rates[tax_index].rate / 100)), desiredCurrency);
	document.getElementById('wtprice_' +i).value	= formatCurrency(workingUnitValue * (1 +(tax_rates[tax_index].rate / 100)), desiredCurrency);
  }
  updateTotalPrices();
  // prepare the page settings for post
  document.getElementById('currencies_code').value  = desiredCurrency;
  document.getElementById('currencies_value').value = new String(newValue);
}

// AJAX auto load SKU pair
function loadSkuDetails(iID, rowCnt) {
  var qty, sku;
  if (document.getElementById('sku').value === text_search) return;
  // check to see if there is a sku present
  if (!iID) sku = document.getElementById('sku').value; // read the search field as the real value
  if (sku == '') return;
  // search if item is aready present then increment it by one
  var numRows = document.getElementById('item_table').rows.length;
  var qty = 1;
  for (var i=1; i<numRows; i++) {
	if (document.getElementById('sku_' +i).value == sku ){
	  qty = document.getElementById('pstd_' +i).value;
	  qty++;
	  var rowCnt = i;
	}
  }
  var cID = document.getElementById('bill_acct_id').value;
  var bID = document.getElementById('store_id').value;
  $.ajax({
    type: "GET",
    contentType: "application/xml; charset=utf-8",
	url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuDetails&bID='+bID+'&cID='+cID+'&qty='+qty+'&iID='+iID+'&strict=1&sku='+sku+'&rID='+rowCnt+'&jID='+journalID,
    dataType: ($.browser.msie) ? "text" : "xml",
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
    },
    success: fillInventory
  });
}

function fillInventory(sXml) {
  var text   = '';
  var newRow = false;
  var exchange_rate = document.getElementById('currencies_value').value;
  var xml    = parseXml(sXml);
  if (!xml) return;
  var sku    = $(xml).find("sku").first().text(); // only the first find, avoids bom add-ons
  if (!sku) return;
  var qty    = parseFloat($(xml).find("qty").first().text());
  var negate = <?php echo $action=='pos_return' ? 'true' : 'false'; ?>;
  if (negate) qty = -qty;
  var rowCnt = $(xml).find("rID").text();
  if (!rowCnt) {
	  rowCnt = document.getElementById('item_table').rows.length - 1;
	  newRow = true;
  }
  document.getElementById('sku_'     +rowCnt).value       = sku;
  document.getElementById('sku_'     +rowCnt).style.color = '';
  document.getElementById('full_'    +rowCnt).value       = formatCurrency($(xml).find("full_price").text() * exchange_rate);
  document.getElementById('inactive_'+rowCnt).value       = $(xml).find("inactive").text();
  document.getElementById('pstd_'    +rowCnt).value       = qty;
  document.getElementById('acct_'    +rowCnt).value       = $(xml).find("account_sales_income").text();
  document.getElementById('price_'   +rowCnt).value       = formatPrecise($(xml).find("sales_price").text() * exchange_rate);
  document.getElementById('wtprice_' +rowCnt).value       = formatCurrency(($(xml).find("sales_price").text() * exchange_rate)* (1+(tax_rates[$(xml).find("item_taxable").text()].rate / 100)));
  if(default_sales_tax == -1) document.getElementById('tax_'   +rowCnt).value     = $(xml).find("item_taxable").text();
  if ($(xml).find("description_sales").text()) {
    document.getElementById('desc_'  +rowCnt).value       = $(xml).find("description_sales").text();
  } else {
    document.getElementById('desc_'  +rowCnt).value       = $(xml).find("description_short").text();
  }
  updateRowTotal(rowCnt, false);
  if(newRow == true) setId = addInvRow();
  document.getElementById('sku').focus();
  document.getElementById('sku').value = '';
//Image handler  
  test = "<?php echo DIR_WS_MY_FILES . $_SESSION['company'] . '/inventory/images/' ?>"+ $(xml).find('image_with_path').text();
  $('#inv_popup > img').attr('src', test);
  $('#curr_image').attr('src', test);
  $('#inv_popup').dialog({ autoOpen:false, width:800 });
}

function monitorPrinting() {
  var applet = document.jZebra;
  if (applet != null) {
    if (!applet.isDonePrinting()) {
      window.setTimeout('monitorPrinting()', 1000);
    } else {
      var e = applet.getException();
      if (e != null) {
	    alert("Exception occured: " + e.getLocalizedMessage());
//	  } else {
//	    window.opener.location.reload();
//	    self.close();
	  }
    }
  } else {
	alert("Error: Java label printing applet not loaded!");
  }
}
function InventoryProp(elementID) {
	  var sku = document.getElementById('sku_'+elementID).value;
	  if (sku != text_search && sku != '') {
	    $.ajax({
	      type: "GET",
	      contentType: "application/json; charset=utf-8",
		  url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuValid&strict=1&sku='+sku,
	      dataType: ($.browser.msie) ? "text" : "xml",
	      error: function(XMLHttpRequest, textStatus, errorThrown) {
	        alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
	      },
		  success: processSkuProp
	    });
	  }
	}

	function processSkuProp(sXml) {
	  var xml = parseXml(sXml);
	  if (!xml) return;
	  if ($(xml).find("id").first().text() != 0) {
		var id = $(xml).find("id").first().text();
		window.open("index.php?module=inventory&page=main&action=properties&cID="+id,"inventory","width=800px,height=600px,resizable=1,scrollbars=1,top=50,left=50");
	  }
	}
// -->
</script>
<script type="text/javascript">
// this part is for the payment div and the ajax saving.
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
var resClockID  = 0;
var cardLength  = 30; // guess size of card to auto convert card information
var skuLength   = <?php echo ORD_BAR_CODE_LENGTH; ?>;
var pay_methods = <?php echo count($payment_modules) ? 'true' : 'false'; ?>;


function activateFields() {
  if (!pay_methods) return;
  var index = document.getElementById('payment_method').selectedIndex;
  for (var i=0; i<document.getElementById('payment_method').options.length; i++) {
   document.getElementById('pm_'+i).style.visibility = 'hidden';
  }
  document.getElementById('pm_'+index).style.visibility = '';
}

function readCard(override) {
  var index    = document.getElementById('payment_method').selectedIndex;
  var method   = document.getElementById('payment_method').options[index].value;
  if (!document.getElementById(method+'_field_0')) return false;
  var entry    = document.getElementById(method+'_field_0').value;
  if (entry.length < cardLength) return false; // not enough characters return
  var eof      = entry.search(/\?/);
  if (!eof) return false;// the end of line character has not been read, return and wait for rest of input.
  clearTimeout(resClockID);
  resClockID   = 0;
  resClockID   = setTimeout("parseCard()", 1000); // wait for the rest of the card to be input
  return true;
}

function parseCard() {
  clearTimeout(resClockID);
  var index    = document.getElementById('payment_method').selectedIndex;
  var method   = document.getElementById('payment_method').options[index].value;
  var entry    = document.getElementById(method+'_field_0').value;
  jQuery.trim(entry);
  // now trim the start if characters were present when scanned
  var bof      = entry.search(/\%B/);
  entry        = entry.substr(bof);
  var caret    = entry.search(/\^/);
  var cardNum  = entry.substr(2, caret-2);
  entry        = entry.substr(caret+1);
  caret        = entry.search(/\^/);
  var cardName = entry.substr(0, caret);
  entry        = entry.substr(caret+1);
  var cardYear = entry.substr(0,2);
  var cardMon  = entry.substr(2,2);
  document.getElementById(method+'_field_0').value = jQuery.trim(cardName);
  document.getElementById(method+'_field_1').value = cardNum;
  document.getElementById(method+'_field_2').value = cardMon;
  document.getElementById(method+'_field_3').value = cardYear;
  if (document.getElementById(method+'_field_3')) {
    document.getElementById(method+'_field_4').focus();
  } else {
    document.getElementById('btn_save').focus();
  }
}

function SavePayment(PrintOrSave) { // request function
  var amount = document.getElementById('amount').value;
  var index  = document.getElementById('payment_method').selectedIndex;
  var method = document.getElementById('payment_method').options[index].value;
  var f0 = document.getElementById(method+'_field_0') ? document.getElementById(method+'_field_0').value : '';
  var f1 = document.getElementById(method+'_field_1') ? document.getElementById(method+'_field_1').value : '';
  var f2 = document.getElementById(method+'_field_2') ? document.getElementById(method+'_field_2').value : '';
  var f3 = document.getElementById(method+'_field_3') ? document.getElementById(method+'_field_3').value : '';
  var f4 = document.getElementById(method+'_field_4') ? document.getElementById(method+'_field_4').value : '';
  var numRows = document.getElementById('pmt_table').rows.length - 1;
  document.getElementById('pdes_'+numRows).value = pmt_types[method];
  document.getElementById('meth_'+numRows).value = method;
  document.getElementById('pmt_'+numRows).value  = amount;
  document.getElementById('f0_'+numRows).value   = f0;
  document.getElementById('f1_'+numRows).value   = f1;
  document.getElementById('f2_'+numRows).value   = f2;
  document.getElementById('f3_'+numRows).value   = f3;
  document.getElementById('f4_'+numRows).value   = f4;
  addPmtRow();
  updateTotalPrices();
  disablePopup();
  if(document.getElementById('bal_due').value == formatCurrency(0)){
	ajaxSave(PrintOrSave);
  }
}

function ajaxSave(PrintOrSave){
	refreshOrderClock(); 
	if (!save_allowed) return;
	save_allowed = false;
	showLoading();
	$.ajax({
		type: "POST",
		url: 'index.php?module=phreepos&page=ajax&op=save_main&action='+PrintOrSave,
		dataType: ($.browser.msie) ? "text" : "xml",
		data: $("form").serialize(),
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		      alert ("Ajax ErrorThrown: " + errorThrown + "\nTextStatus: " + textStatus + "\nError: " + XMLHttpRequest.responseText);
		      save_allowed = true;
			},
		success: ajaxPrintAndClean
	  });
	hideLoading();
}

//java label printing
function ajaxPrintAndClean(sXml) { // call back function
  save_allowed = true;
  var xml = parseXml(sXml);
  var applet = document.jZebra;
  if (!xml) return;
  var massage 		= $(xml).find("massage").text();
  if ( massage ) alert( massage );
  var action 		= $(xml).find("action").text();
  var opendrawer   	= $(xml).find("open_cash_drawer").text(); 
  var print 		= action.substring(0,5) == 'print';
  if ( print && applet != null ) { 
		<?php 
		if (defined('PHREEPOS_RECEIPT_PRINTER_STARTING_LINE') && PHREEPOS_RECEIPT_PRINTER_STARTING_LINE <> '') {
	      foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_STARTING_LINE) as $key=>$line) {
		    $temp = '';
		    foreach(explode(":",$line) as $key=>$char) {
		      $temp .=chr($char);
		    }
		    echo'applet.append("'.$temp .'"+"\n");'.chr(13);
		  }
		}
		?>
		$(xml).find("receipt_data").each(function() {
			applet.append($(this).find("line").text() + "\n");
		});
		if (opendrawer == true){
<?php       foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER) as $key=>$line) {
				$temp = '';
			   	foreach(explode(":",$line) as $key=>$char) $temp .=chr($char);
			    	echo'applet.append("'.$temp .'"+"\n");'.chr(13);
				} ?>
			
		}
		<?php 
		if (defined('PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE') && PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE <> '') {
			foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE) as $key=>$line) {
				$temp = '';
		    	foreach(explode(":",$line) as $key=>$char) {
			  		$temp .=chr($char);
		     		}
		    	echo'applet.append("'.$temp .'"+"\n");'.chr(13);
		  	}
		}				?>
		applet.print();
		monitorPrinting();
	}else if( print ){
		var order_id = $(xml).find("order_id").text();
		var printWin = window.open("index.php?module=phreeform&page=popup_gen&gID= <?php echo POPUP_FORM_TYPE;?> &date=a&xfld=journal_main.id&xcr=EQUAL&xmin=" + order_id ,"reportFilter","width=700px,height=550px,resizable=1,scrollbars=1,top=150px,left=200px");
		printWin.focus();	
	}
	resetForm();
}
/*
 *printing previous reciept by this admin user 
 *
 */
function GetPrintPreviousReceipt() {
	$.ajax({
	    type: "GET",
        contentType: "application/json; charset=utf-8",
        url: 'index.php?module=phreepos&page=ajax&op=print_previous',
        dataType: ($.browser.msie) ? "text" : "xml",
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
        },
        success: PrintPreviousReceipt
     });
   }

function PrintPreviousReceipt(sXml) { // call back function
	  var xml = parseXml(sXml);
	  var applet = document.jZebra;
	  if (!xml) return;
	  var massage = $(xml).find("massage").text();
	  if ( massage ) alert( massage );
	  if (applet != null) { 
	        <?php 
	        if (defined('PHREEPOS_RECEIPT_PRINTER_STARTING_LINE') && PHREEPOS_RECEIPT_PRINTER_STARTING_LINE <> '') {
	          foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_STARTING_LINE) as $key=>$line) {
	            $temp = '';
	            foreach(explode(":",$line) as $key=>$char) {
	              $temp .=chr($char);
	            }
	            echo'applet.append("'.$temp .'"+"\n");'.chr(13);
	          }
	        }
	        ?>
	        $(xml).find("receipt_data").each(function() {
	            applet.append($(this).find("line").text() + "\n");
	        });
			if ($(xml).find("open_cash_drawer").text() == 1){
		        <?php 
		        if (defined('PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER') && PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER <> '') {
		            foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER) as $key=>$line) {
		                $temp = '';
		                foreach(explode(":",$line) as $key=>$char) $temp .=chr($char);
		                echo'applet.append("'.$temp .'"+"\n");'.chr(13);
		            }
		        }
		        ?>

			}
	        <?php 
	        if (defined('PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE') && PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE <> '') {
	            foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_CLOSING_LINE) as $key=>$line) {
	                $temp = '';
	                foreach(explode(":",$line) as $key=>$char) {
	                    $temp .=chr($char);
	                    }
	                echo'applet.append("'.$temp .'"+"\n");'.chr(13);
	            }
	        }               ?>
	        applet.print();
	        monitorPrinting();
	    }else{
	        var order_id = $(xml).find("order_id").text();
	        var printWin = window.open("index.php?module=phreeform&page=popup_gen&gID= <?php echo POPUP_FORM_TYPE;?> &date=a&xfld=journal_main.id&xcr=EQUAL&xmin=" + order_id ,"reportFilter","width=700px,height=550px,resizable=1,scrollbars=1,top=150px,left=200px");
	        printWin.focus();   
	    }
	}

function OpenDrawer(){
	var applet = document.jZebra;
	if (applet != null) {
	<?php        
		foreach(explode(",",PHREEPOS_RECEIPT_PRINTER_OPEN_DRAWER) as $key=>$line) {
			$temp = '';
	    	foreach(explode(":",$line) as $key=>$char) $temp .=chr($char);
	    	echo'applet.append("'.$temp .'"+"\n");'.chr(13);
		}
	?>
		applet.print();
	}
}
//<!-- javascript for ajax popup

//0 means disabled; 1 means enabled;  
var popupStatus = 0;  

//loading popup with jQuery magic!  
function popupPayment(){  
	//loads popup only if it is disabled
	if(popupStatus==0){  
		$("#backgroundPopup").fadeIn("slow");  
		$("#popupPayment").fadeIn("slow");  
		popupStatus = 1;
		document.getElementById('amount').value = document.getElementById('bal_due').value;
		activateFields();
		document.getElementById('payment_method').focus();
	}
}  

//disabling popup with jQuery magic!  
function disablePopup(){  
	//disables popup only if it is enabled  
	if(popupStatus==1){  
		$("#backgroundPopup").fadeOut("slow");  
		$("#popupPayment").fadeOut("slow");  
		popupStatus = 0;  
	}  
}  

//centering popup  
function centerPopup(){  
	//request data for centering  
	var windowWidth = document.documentElement.clientWidth;  
	var windowHeight = document.documentElement.clientHeight;  
	var popupHeight = $("#popupPayment").height();  
	var popupWidth = $("#popupPayment").width();  
	//centering  
	$("#popupPayment").css({  
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,  
		"left": windowWidth/2-popupWidth/2  
	});  
	$("#backgroundPopup").css({
		"position": "absolute",
		"opacity": "0.7",
		"background":"#000000",  
		"top": "0px",  
		"left": "0px",
		"height": windowHeight,  
		"width":windowWidth	  
	});

}  
// image functions
function showImage() {
    $('#inv_popup').dialog('open'); 
}

$(document).ready(function(){
//CLOSING POPUP  
//Click out event!  
	$("#backgroundPopup").click(function(){
		disablePopup();  
	});
	  
});
//Press Escape event!  
$(document).keypress(function(e){  
	if(e.keyCode==27 && popupStatus==1){  
		disablePopup();  
	}  
});  
//-->

</script>
<style>
h1{
	position:fixed;
    _position:absolute; /* hack for internet explorer 6*/ 
    left:350px;  
    top:0px;
    margin-top: -5px;
    font-size:3em; 
}

#inv_image{
    position:fixed;
    _position:absolute; /* hack for internet explorer 6*/ 
    left:400px;  
    top:35px;  
    border:1px solid #cecece;
}
<!-- styles for ajax popup

#backgroundPopup{
	display:none;
	position:fixed;
	_position:absolute; /* hack for internet explorer 6*/ 
	border:1px solid #cecece;
	z-index:10;
}
#popupPayment{    
	display:none;  
	position:fixed;  
	_position:absolute; /* hack for internet explorer 6*/  
	height:400px;  
	width:408px;  
	background:#FFFFFF;  
	border:2px solid #cecece;  
	z-index:20;  
	padding:12px;  
	font-size:13px;  
}  

#popupPaymentClose{  
	font-size:14px;  
	line-height:14px;  
	right:6px;  
	top:4px;  
	position:absolute;  
	color:#6fa5fd;  
	font-weight:700;  
	display:block;  
}  
-->
</style>
