
<script type="text/javascript">
<?php echo $FirstValue;?>;
<?php echo $FirstId; ?>;
<?php echo $SecondField; ?>;
<?php echo $SecondFieldValue; ?>;
<?php echo $SecondFieldId; ?>;
var text_no 			=<?php echo '"'. TEXT_NO.'"'; ?>;
var text_yes			=<?php echo '"'.TEXT_YES.'"'; ?>;
var filter_equal_to 	=<?php echo '"'.FILTER_EQUAL_TO.'"';?>;
var filter_not_equal_to =<?php echo '"'.FILTER_NOT_EQUAL_TO.'"';?>;
var filter_like			=<?php echo '"'.FILTER_LIKE.'"';?>;
var filter_not_like		=<?php echo '"'.FILTER_NOT_LIKE.'"';?>;
var filter_bigger_than	=<?php echo '"'.FILTER_BIGGER_THAN.'"';?>;
var filter_less_than	=<?php echo '"'.FILTER_LESS_THAN.'"';?>;
var filter_contains		=<?php echo '"'.FILTER_CONTAINS.'"';?>;
var text_properties     =<?php echo '"'.TEXT_PROPERTIES.'"';?>;
</script>

<script type="text/javascript" src="modules/inventory/custom/pages/main/print_label.js"></script>
<script type="text/javascript" src="modules/inventory/custom/pages/main/filter.js"></script>
<script type="text/javascript">

function init() {
	$(function() { $('#detailtabs').tabs(); });
	$('#inv_image').dialog({ autoOpen:false, width:800 });
 	<?php if ($action <> 'new' && $action <> 'edit') { // set focus for main window
		echo "  document.getElementById('search_text').focus();";
		echo "  document.getElementById('search_text').select();";
  	}
  	if ($action == 'properties' || $action == 'edit') { 
  		echo '  onstart();';
  	} ?>
  	<?php if ($action == 'edit' && $cInfo->inventory_type == 'ms') { // set focus for main window
		echo '  masterStockTitle(0);';
		echo '  masterStockTitle(1);';
		echo '  masterStockBuildSkus();';
  	} ?>
  	 		
}

function processSkuDetails(sXml) { // call back function
	var text = '';
	var xml = parseXml(sXml);
	if (!xml) return;
	var rowID = $(xml).find("rID").text();
	document.getElementById('sku_'+rowID).value              = $(xml).find("sku").text();
	document.getElementById('sku_'+rowID).style.color        = '';
	document.getElementById('desc_'+rowID).value             = $(xml).find("description_short").text();
	if(document.getElementById('qty_'+rowID).value == 0)       document.getElementById('qty_'+rowID).value = 1;
	document.getElementById('item_cost_'+rowID).value        = formatCurrency($(xml).find("item_cost").text());
	document.getElementById('sales_price_'+rowID).value      = formatCurrency($(xml).find("sales_price").text());
	updateTotalValues();	 
}

function addBOMRow() {
	var cell = Array(6);
	var newRow = document.getElementById("bom_table").insertRow(-1);
	var newCell;
	rowCnt = newRow.rowIndex;
	// NOTE: any change here also need to be made below for reload if action fails
	cell[0] = '<td align="center">';
	cell[0] += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', image_delete_text, 'style="cursor:pointer" onclick="if (confirm(\''+image_delete_msg+'\')) removeBOMRow('+rowCnt+');"') + '<\/td>';
	cell[1] = '<td align="center">';
	// Hidden fields
	cell[1] += '<input type="hidden" name="id_'+rowCnt+'" id="id_'+rowCnt+'" value="">';
	// End hidden fields
	cell[1] += '<input type="text" name="assy_sku[]" id="sku_'+rowCnt+'" value="" size="<?php echo (MAX_INVENTORY_SKU_LENGTH + 1); ?>" onchange="bom_guess('+rowCnt+')" maxlength="<?php echo MAX_INVENTORY_SKU_LENGTH; ?>">&nbsp;';
	cell[1] += buildIcon(icon_path+'16x16/actions/system-search.png', text_sku, 'align="top" style="cursor:pointer" onclick="InventoryList('+rowCnt+')"') + '&nbsp;<\/td>';
	cell[1] += buildIcon(icon_path+'16x16/actions/document-properties.png', text_properties, 'id="sku_prop_'+rowCnt+'" align="top" style="cursor:pointer" onclick="InventoryProp('+rowCnt+')"');
	cell[2] = '<td><input type="text" name="assy_desc[]" id="desc_'+rowCnt+'" value="" size="64" maxlength="64"><\/td>';
	cell[3] = '<td><input type="text" name="assy_qty[]" id="qty_'+rowCnt+'" value="0" size="6" maxlength="5"><\/td>';
	cell[4] = '<td><input type="text" name="assy_item_cost[]" id="item_cost_'+rowCnt+'" value="0" size="6" maxlength="5"><\/td>';
	cell[5] = '<td><input type="text" name="assy_sales_price[]" id="sales_price_'+rowCnt+'" value="0" size="6" maxlength="5"><\/td>';

	for (var i=0; i<cell.length; i++) {
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = cell[i];
	}

	return rowCnt;
}

function bom_guess(rID){
	var sku = document.getElementById('sku_'+rID).value;
	if (sku != text_search && sku != '') {
	  $.ajax({
	      type: "GET",
	      contentType: "application/json; charset=utf-8",
		  url: 'index.php?module=inventory&page=ajax&op=inv_details&fID=skuDetails&sku='+sku+'&rID='+rID,
	      dataType: ($.browser.msie) ? "text" : "xml",
	      error: function(XMLHttpRequest, textStatus, errorThrown) {
	        alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
	      },
		  success: processSkuDetails
	    });
	}
}

function InventoryProp(rID) {
	var sku = document.getElementById('sku_'+rID).value;
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
// ******* EOF - AJAX BOM Cost function pair *********/
function bomTotalValues(){
	var numRows = document.getElementById('bom_table').rows.length;
	var itemCost = null;
	var salesPrice = null;
	for (var i=1; i<numRows+1; i++) {
		var qty             = parseFloat(document.getElementById('qty_' + i ).value);
		var unit_itemCost   = parseFloat(cleanCurrency(document.getElementById('item_cost_'   + i ).value));
		var unit_salesPrice = parseFloat(cleanCurrency(document.getElementById('sales_price_' + i ).value));
		total_itemCost   = qty * unit_itemCost;
		total_salesPrice = qty * unit_salesPrice;
		itemCost   = itemCost + total_itemCost ;
		salesPrice = salesPrice  + total_salesPrice ;
	}
	document.getElementById('total_item_cost').value   = formatCurrency( itemCost );
	document.getElementById('total_sales_price').value = formatCurrency( salesPrice );
}

$(document).ready(function(){
	//event for change of textbox
	$("#full_price").blur(function(){
		update_full_price_incl_tax(true, true, false);
	});
	$("#full_price").change(function(){
		update_full_price_incl_tax(true, true, false);
	});
	$("#full_price").keydown(function(e) {
		    str = document.getElementById('full_price').value;
		    document.getElementById('full_price').value = str.replace('.', ",");
	});
	
	$("#item_taxable").change(function(){
		update_full_price_incl_tax(true, true, false);
	});
	
	$("#marge").blur(function(){
		marge = document.getElementById('marge').value;
		text = formatCurrency(cleanCurrency(document.getElementById('item_cost').value) * marge );
		document.getElementById('full_price_incl_tax' ).value = text;
		update_full_price_incl_tax(false, false, true);
	});  
	$("#marge").change(function(){
		marge = document.getElementById('marge').value;
		text = formatCurrency(cleanCurrency(document.getElementById('item_cost').value) * marge );
		document.getElementById('full_price_incl_tax' ).value = text;
		update_full_price_incl_tax(false, false, true);
	});
	$("#marge").keydown(function(e) {
	    str = document.getElementById('marge').value;
	    document.getElementById('marge').value = str.replace(',', ".");
	});
	
	$("#item_cost").blur(function(){
		var retour = prompt ("toets een 1 om 'marge' te herberekenen \ntoets een 2 om 'verkoop prijs' te herberekenen",'1')
		if (retour == '1'){
			update_full_price_incl_tax(true, true, false);
		}else if (retour == '2'){
			marge = document.getElementById('marge').value;
			text = formatCurrency(cleanCurrency(document.getElementById('item_cost').value) * marge );
			document.getElementById('full_price_incl_tax' ).value = text;
			update_full_price_incl_tax(false, false, true);
		}
	});
	$("#item_cost").change(function(){
		var retour = prompt ("toets een 1 om 'marge' te herberekenen \ntoets een 2 om 'verkoop prijs' te herberekenen",'1')
		if (retour == '1'){
			update_full_price_incl_tax(true, true, false);
		}else if (retour == '2'){
			marge = document.getElementById('marge').value;
			text = formatCurrency(cleanCurrency(document.getElementById('item_cost').value) * marge );
			document.getElementById('full_price_incl_tax' ).value = text;
			update_full_price_incl_tax(false, false, true);
		}
	});
	$("#item_cost").keydown(function(e) {
	    str = document.getElementById('item_cost').value;
	    document.getElementById('item_cost').value = str.replace('.', ",");
	});  

	$("#full_price_incl_tax").blur(function(){
		update_full_price_incl_tax(true, false, true);
	});
	$("#full_price_incl_tax").change(function(){
		update_full_price_incl_tax(true, false, true);
	});
	$("#full_price_incl_tax").keydown(function(e) {
	    str = document.getElementById('full_price_incl_tax').value;
	    document.getElementById('full_price_incl_tax').value = str.replace('.', ",");
	});  
		
});


function update_full_price_incl_tax(marge, inclTax, fullprice){
	
//marge berekenen
	if(marge){
		if(document.getElementById('item_cost').value !== '' && document.getElementById('full_price_incl_tax' ).value !==''){
			document.getElementById('marge').value = cleanCurrency(document.getElementById('full_price_incl_tax' ).value) / cleanCurrency(document.getElementById('item_cost').value); 
		}else{
			document.getElementById('marge').value ='';
		}
	}
//volledige prijs incl btw berekenen	
	if(inclTax){
		if(document.getElementById('full_price' ).value!== '' && document.getElementById('item_taxable' ).value!== ''){
			tax_index = document.getElementById('item_taxable' ).value;
			text = formatCurrency(cleanCurrency(document.getElementById('full_price' ).value)* (1+(tax_rates[tax_index].rate / 100)));
			document.getElementById('full_price_incl_tax' ).value = text;
		}else{
			document.getElementById('full_price_incl_tax' ).value = '';
		}
	}
//volledige prijs berekenen	
	if(fullprice){
		if(document.getElementById('full_price_incl_tax' ).value !== '' && document.getElementById('item_taxable' ).value!== ''){
			tax_index = document.getElementById('item_taxable' ).value;
			text = formatCurrency(cleanCurrency(document.getElementById('full_price_incl_tax').value) / (1+(tax_rates[tax_index].rate / 100)));
			document.getElementById('full_price' ).value = text;
		}else{
			document.getElementById('full_price' ).value = '';
		}
	}
//controle op de verkoopprijs
	document.getElementById('full_price_incl_tax' ).value = formatCurrency(cleanCurrency(document.getElementById('full_price_incl_tax' ).value));
	var tax_index = document.getElementById('item_taxable' ).value;
	var text = formatCurrency(cleanCurrency(document.getElementById('full_price' ).value)* (1+(tax_rates[tax_index].rate / 100)));
	var full = document.getElementById('full_price_incl_tax' ).value;
	if(full !== text ){
		$("#full_price_incl_tax").css({  
			"background":"#FF3300"
		});  
	}else {
		$("#full_price_incl_tax").css({  
			"background":"#FFFFFF"
		});
	}
}

function onstart(){
	if(document.getElementById('full_price' ).value!== '' && document.getElementById('item_taxable' ).value!== ''){
		tax_index = document.getElementById('item_taxable' ).value;
		text = formatCurrency(cleanCurrency(document.getElementById('full_price' ).value)* (1+(tax_rates[tax_index].rate / 100)));
		document.getElementById('full_price_incl_tax' ).value     = text;
		$("#old_full_price_incl_tax" ).append ( '<i>'+ text +'</i>');
		if(document.getElementById('item_cost').value!== ''){
			document.getElementById('marge').value     = cleanCurrency(document.getElementById('full_price_incl_tax' ).value) / cleanCurrency(document.getElementById('item_cost').value);
			$("#old_marge" ).append ( '<i>'+cleanCurrency(document.getElementById('full_price_incl_tax' ).value) / cleanCurrency(document.getElementById('item_cost').value)+'</i>');
		}
	}
}

function salesTaxes(id, text, rate) {
	  this.id   = id;
	  this.text = text;
	  this.rate = rate;
	}

function beforePrint(){
	var price = document.getElementById('full_price_incl_tax' ).value;
	var sku   = document.getElementById('sku' ).value;
	var desc  = document.getElementById('description_sales' ).value;
	var qty   = document.getElementById('purch_package_quantity' ).value;
	if(isNaN(qty) || qty == 0) qty = 1;
	print( sku, price, desc, qty );
}
<?php echo $js_tax_rates;?>


<?php if ($include_template == 'template_main.php'){?>
$(document).keydown(function(e) {
    //alert(e.keyCode);
    if(e.keyCode == 13) {
    	submitToDo('filter'); 
    }
 });
<?php }?>
</script>

