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
//  Path: /modules/inventory/pages/adjustments/js_include.php
//

?>
<script type="text/javascript">
<!--
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
var adj_qty                = 0;
var unit_price_placeholder = false;
var unit_price_note        = '<?php echo JS_COGS_AUTO_CALC; ?>';
var securityLevel          = <?php echo $security_level; ?>;
<?php echo js_calendar_init($cal_adj); ?>

function init() {
  document.getElementById('sku_1').focus();
<?php if ($action == 'edit') echo '  EditAdjustment(' . $oID . ')'; ?>

}

function check_form() {
  var error = 0;
  var error_message = '<?php echo JS_ERROR; ?>';

  var sku = document.getElementById('sku_1').value;
  if (sku == '') { // check for sku not blank
  	error_message += '<?php echo JS_NO_SKU_ENTERED; ?>';
	error = 1;
  }

  var qty = document.getElementById('adj_qty').value;
  if (qty == '' || qty == '0') { // check for quantity non-zero
  	error_message += '<?php echo JS_ADJ_VALUE_ZERO; ?>';
	error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  }
  return true;
}

// Insert other page specific functions here.
function clearForm() {
  document.getElementById('id').value                  = 0;
  document.getElementById('store_id').value            = 0;
  document.getElementById('purchase_invoice_id').value = '';
  document.getElementById('post_date').value           = '<?php echo date(DATE_FORMAT, time()); ?>';
  document.getElementById('adj_reason').value          = '';
  document.getElementById('acct_1').value              = '';
  document.getElementById('sku_1').value               = '';
  document.getElementById('serial_1').value            = '';
  document.getElementById('desc_1').value              = '';
  document.getElementById('stock_1').value             = '0';
  document.getElementById('price_1').value             = '';
  document.getElementById('adj_qty').value             = '';
  document.getElementById('balance').value             = '';
}

function InventoryList(rowCnt) {
  var bID = document.getElementById('store_id').value;
  var sku = document.getElementById('sku_1').value;
  window.open("index.php?module=inventory&page=popup_inv&list=1&type=v&storeID="+bID+"&search_text="+sku,"inventory","width=700,height=550,resizable=1,scrollbars=1,top=150,left=200");
}

function OpenAdjList() {
  clearForm();
  window.open("index.php?module=inventory&page=popup_adj&list=1&form=inv_adj","inv_adj_open","width=700,height=550,resizable=1,scrollbars=1,top=150,left=200");
}

function loadSkuDetails(iID) {
  var bID = document.getElementById('store_id').value;
    $.ajax({
      type: "GET",
      contentType: "application/json; charset=utf-8",
	  url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuDetails&iID='+iID+'&bID='+bID,
      dataType: ($.browser.msie) ? "text" : "xml",
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
      },
	  success: processSkuDetails
    });
}

function processSkuDetails(sXml) { // call back function
  var text = '';
  var xml = parseXml(sXml);
  if (!xml) return;
  document.getElementById('price_1').value     = formatPrecise($(xml).find("item_cost").text());
  document.getElementById('acct_1').value      = $(xml).find("account_cost_of_sales").text();
  document.getElementById('stock_1').value     = $(xml).find("branch_qty_in_stock").text();
  document.getElementById('sku_1').value       = $(xml).find("sku").text();
  document.getElementById('sku_1').style.color = '';
  document.getElementById('desc_1').value      = $(xml).find("description_purchase").text();
  var type = $(xml).find("inventory_type").text();
  if (type=='sr' || type=='sa') document.getElementById('serial_row').style.display = '';
}

function EditAdjustment(rID) {
  $.ajax({
    type: "GET",
    contentType: "application/json; charset=utf-8",
    url: 'index.php?module=phreebooks&page=ajax&op=load_record&rID='+rID,
    dataType: ($.browser.msie) ? "text" : "xml",
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
    },
	success: processEditAdjustment
  });
}

function processEditAdjustment(sXml) {
  var sku, qty;
  var xml = parseXml(sXml);
  if (!xml) return;
  var id = $(xml).find("id").first().text();
  document.getElementById('id').value                  = id;
  document.getElementById('store_id').value            = $(xml).find("store_id").text();
  document.getElementById('purchase_invoice_id').value = $(xml).find("purchase_invoice_id").text();
  document.getElementById('post_date').value           = formatDate($(xml).find("post_date").first().text());
  // turn off some icons
  if (id && securityLevel < 3) removeElement('tb_main_0', 'tb_icon_save');
  // fill item rows
  $(xml).find("items").each(function() {
	var type = $(this).find("gl_type").text();
	switch (type) {
	  case 'ttl':
		document.getElementById('adj_reason').value = $(this).find("description").text();
		document.getElementById('acct_1').value     = $(this).find("gl_account").text();
	    break;
	  case 'adj':
		sku = $(this).find("sku").text();
		qty = $(this).find("qty").text();
		document.getElementById('sku_1').value      = sku;
		document.getElementById('serial_1').value   = $(this).find("serialize").text();
		document.getElementById('desc_1').value     = $(this).find("description").text();
		document.getElementById('price_1').value    = formatPrecise($(this).find("debit_amount").text() / qty);
		document.getElementById('adj_qty').value    = qty;
		adj_qty = qty;
	  default: // do nothing
	}
  });
  loadSkuStock(sku);
}

function loadSkuStock(sku) {
  var bID = document.getElementById('store_id').value;
	$.ajax({
	  type: "GET",
	  contentType: "application/json; charset=utf-8",
	  url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuStock&sku='+sku+'&bID='+bID,
	  dataType: ($.browser.msie) ? "text" : "xml",
	  error: function(XMLHttpRequest, textStatus, errorThrown) {
		alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
	  },
	  success: processSkuStock
	});
  updateBalance()
}

function processSkuStock(sXml) { // call back function
  var text = '';
  var xml = parseXml(sXml);
  if (!xml) return;
  document.getElementById('stock_1').value = $(xml).find("branch_qty_in_stock").text() - adj_qty;
  updateBalance();
}

function updateBalance() {
  var stock = parseFloat(document.getElementById('stock_1').value);
  var adj   = parseFloat(document.getElementById('adj_qty').value);
  document.getElementById('balance').value = stock + adj;
  if (adj < 0) {
	unit_price_placeholder = document.getElementById('price_1').value;
	document.getElementById('price_1').value = '';
	document.getElementById('price_1').readOnly = true;
	if (document.all) { // IE browsers
	  document.getElementById('unit_price_id').innerText = unit_price_note;
	} else { //firefox
	  document.getElementById('unit_price_id').textContent = unit_price_note;
	}
  } else {
	if (unit_price_placeholder) document.getElementById('price_1').value = unit_price_placeholder;
	document.getElementById('price_1').readOnly = false;	
	if(document.all) { // IE browsers
	  document.getElementById('unit_price_id').innerText = '';
	} else { //firefox
	  document.getElementById('unit_price_id').textContent = '';
	}
  }
}

// -->
</script>