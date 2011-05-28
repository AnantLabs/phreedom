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
//  Path: /modules/inventory/pages/transfer/js_include.php
//
?>
<script type="text/javascript">
<!--
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
<?php echo js_calendar_init($cal_xfr); ?>

function init() {
}

function check_form() {
  var error = 0;
  var error_message = '<?php echo JS_ERROR; ?>';

  if (error == 1) {
    alert(error_message);
    return false;
  }
  return true;
}

// Insert other page specific functions here.
function InventoryList(rowCnt) {
  var bID = document.getElementById('source_store_id').value;
  var sku = document.getElementById('sku_'+rowCnt).value;
  window.open("index.php?module=inventory&page=popup_inv&rowID="+rowCnt+"&type=v&storeID="+bID+"&f1=cog&search_text="+sku,"inventory","width=700,height=550,resizable=1,scrollbars=1,top=150,left=200");
}

function serialList(rowID) {
  var choice    = document.getElementById('serial_'+rowID).value;
  var newChoice = prompt('<?php echo 'Enter Serial Number:'; ?>', choice);
  if (newChoice) document.getElementById('serial_'+rowID).value = newChoice;
}

function loadSkuDetails(iID, rID) {
  if (!rID) return;
  var bID = document.getElementById('source_store_id').value;
  $.ajax({
    type: "GET",
    contentType: "application/json; charset=utf-8",
    url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuDetails&iID='+iID+'&bID='+bID+'&rID='+rID,
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
  var type   = $(xml).find("inventory_type").text();
  var rowCnt = $(xml).find("rID").text();
  document.getElementById('sku_'  +rowCnt).value       = $(xml).find("sku").text();
  document.getElementById('sku_'  +rowCnt).style.color = '';
  document.getElementById('desc_' +rowCnt).value       = $(xml).find("description_short").text();
  document.getElementById('stock_'+rowCnt).value       = $(xml).find("branch_qty_in_stock").text();
  document.getElementById('acct_'+rowCnt).value        = $(xml).find("account_inventory_wage").text();
  updateBalance();
}

function updateBalance() {
  for (var i=1; i<document.getElementById('item_table').rows.length; i++) {
    var stock = parseFloat(document.getElementById('stock_'+i).value);
	if (isNaN(stock)) stock = 0;
    var adj   = parseFloat(document.getElementById('qty_'+i).value);
	if (isNaN(adj)) adj = 0;
    document.getElementById('bal_'+i).value = stock - adj;
  }
}

function addInvRow() {
  var wrap    = new Array();
  var cell   = new Array();
  var newRow = document.getElementById("item_table").insertRow(-1);
  var rowCnt = newRow.rowIndex;
  // NOTE: any change here also need to be intemplate form for reload if action fails
  cell[0]  = buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', '<?php echo TEXT_DELETE; ?>', 'style="cursor:pointer" onclick="if (confirm(\'<?php echo TEXT_ROW_DELETE_ALERT; ?>\')) removeInvRow('+rowCnt+');"');
  cell[1]  = '    <input type="text" name="qty_'+rowCnt+'" id="qty_'+rowCnt+'" style="text-align:right" size="6" maxlength="5" onchange="updateBalance('+rowCnt+')">';
  cell[2]  = '    <input type="text" name="stock_'+rowCnt+'" id="stock_'+rowCnt+'" readonly="readonly" style="text-align:right" size="6" maxlength="5">';
  cell[3]  = '    <input type="text" name="bal_'+rowCnt+'" id="bal_'+rowCnt+'" readonly="readonly" style="text-align:right" size="6" maxlength="5">';
  cell[4]  = '    <input type="text" name="sku_'+rowCnt+'" id="sku_'+rowCnt+'" size="<?php echo (MAX_INVENTORY_SKU_LENGTH + 1); ?>" maxlength="<?php echo MAX_INVENTORY_SKU_LENGTH; ?>">';
  cell[4] += buildIcon(icon_path+'16x16/actions/system-search.png', '<?php echo TEXT_SEARCH; ?>', 'style="cursor:pointer" onclick="InventoryList('+rowCnt+')"');
  cell[4] += buildIcon(icon_path+'16x16/actions/tab-new.png', '<?php echo TEXT_SERIAL_NUMBER; ?>', 'style="cursor:pointer" onclick="serialList('+rowCnt+')"');
// Hidden fields
  cell[4] += '<input type="hidden" name="serial_'+rowCnt+'" id="serial_'+rowCnt+'" value="" />';
  cell[4] += '<input type="hidden" name="acct_'+rowCnt+'" id="acct_'+rowCnt+'" value="" />';
// End hidden fields
  cell[5]  = '    <input type="text" name="desc_'+rowCnt+'" id="desc_'+rowCnt+'" size="64" maxlength="63">';
  wrap[4]     = 'nowrap';
  for (var i=0; i<cell.length; i++) {
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = cell[i];
	if (wrap[i]) newCell.nowrap = wrap[i];
  }
  return rowCnt;
}

function removeInvRow(delRowCnt) {
  var glIndex = delRowCnt;
  for (var i=delRowCnt; i<document.getElementById("item_table").rows.length-1; i++) {
	document.getElementById('qty_'+i).value   = document.getElementById('qty_'+(i+1)).value;
	document.getElementById('stock_'+i).value = document.getElementById('stock_'+(i+1)).value;
	document.getElementById('bal_'+i).value   = document.getElementById('bal_'+(i+1)).value;
	document.getElementById('sku_'+i).value   = document.getElementById('sku_'+(i+1)).value;
	document.getElementById('desc_'+i).value  = document.getElementById('desc_'+(i+1)).value;
// Hidden fields
	document.getElementById('serial_'+i).value= document.getElementById('serial_'+(i+1)).value;
	document.getElementById('acct_'+i).value  = document.getElementById('acct_'+(i+1)).value;
// End hidden fields
	glIndex++; // increment the row counter (two rows per entry)
  }
  document.getElementById("item_table").deleteRow(-1);
  updateBalance();
}

// -->
</script>