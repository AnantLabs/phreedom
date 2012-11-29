 
function updateFilter(rowCnt, start){
	var text 	 = document.getElementById('filter_field'+ rowCnt ).value;
	var RowCells = document.getElementById('filter_table').rows[rowCnt].cells;
	switch (SecondField[text]) {
		case  'multi_check_box':
			RowCells[2].innerHTML =	'<input type="text" name="filter_criteria[]" readonly  id="filter_criteria' + rowCnt + '"  value="'+ filter_contains +'" />';
			break;
		default:
			var tempValue = new Array( filter_equal_to , filter_not_equal_to , filter_like , filter_not_like, filter_bigger_than , filter_less_than );
	    	var tempId    = new Array("0","1","2","3","4","5");
	    	RowCells[2].innerHTML =	'<select name="filter_criteria[]" id="filter_criteria'+ rowCnt + '" ></select>';
	    	buildSelect('filter_criteria'+ rowCnt, tempValue, tempId);
	}
	switch (SecondField[text]) {
    	case  'drop_down':
    	case  'multi_check_box':
    	case  'radio': 
        	var tempValue 	=  SecondFieldId[text];
        	var tempId     	=  SecondFieldValue[text];
        	RowCells[3].innerHTML =	'<select name="filter_value[]" id="filter_value'+ rowCnt + '" ></select>';
        	buildSelect('filter_value'+ rowCnt, tempValue, tempId);
    		break;
        case  'check_box':
        	if (typeFilterValue == 'SELECT' ) valueFilterValue = '';
        	var tempValue = new Array(text_no, text_yes);
        	var tempId    = new Array("0","1");
        	RowCells[3].innerHTML =	'<select name="filter_value[]" id="filter_value'+ rowCnt + '" ></select>';
        	buildSelect('filter_value'+ rowCnt, tempValue, tempId);
    		break;
    	default:
    		if(!start ){
    			var typeFilterValue  = document.getElementById('filter_value'+ rowCnt ).tagName;
    			var valueFilterValue = document.getElementById('filter_value'+ rowCnt ).value;
    			if (typeFilterValue != 'INPUT') valueFilterValue = '';
    			RowCells[3].innerHTML = '<input type="text" name="filter_value[]" id="filter_value' + rowCnt + '" size="64" maxlength="64" value="'+valueFilterValue+'" />';
    		}else {
    			RowCells[3].innerHTML = '<input type="text" name="filter_value[]" id="filter_value' + rowCnt + '" size="64" maxlength="64" />';
    		}	    		
   	}	
}

function addFilterRow(){
	var newCell;
	var cell;
	var newRow  = document.getElementById('filter_table_body').insertRow(-1);
	var rowCnt  = newRow.rowIndex;
	newRow.id =  rowCnt;
	cell  = '<td align="center" >';
	cell += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', image_delete_text, 'onClick="removeFilterRow('+rowCnt+')"') + '</td>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = cell;

	cell  = '   <td">';
	cell +=		'<select name="filter_field[]" id="filter_field'+ rowCnt + '" onChange="updateFilter('+ rowCnt + ', false)"></select>';
	cell += '   </td>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = cell;
	cell  = '   <td">';
	cell += '   </td>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = cell;
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = cell;
	buildSelect('filter_field'+ rowCnt, FirstValue, FirstId);
	updateFilter(rowCnt, true);
	  
}

function buildSelect(selElement, value, id) {
	  
	  for (i=0; i<value.length; i++) {
		newOpt = document.createElement("option");
		newOpt.text = value[i];
		document.getElementById(selElement).options.add(newOpt);
		document.getElementById(selElement).options[i].value = id[i];
	  }
	}

function removeFilterRow(rowCnt) {
	$('#'+rowCnt).remove();
}

function TableStartValues( rowCnt, valueFilterField, valueCriteriaField, valueValueField){
	document.getElementById('filter_field'+ rowCnt ).value    = valueFilterField ;
	updateFilter(rowCnt, true);
	document.getElementById('filter_criteria'+ rowCnt ).value = valueCriteriaField;
	document.getElementById('filter_value'+ rowCnt ).value = valueValueField;
}

$(document).ready(function(){
	$("#tb_search_0").hide() ;
});
