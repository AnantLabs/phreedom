<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2010 PhreeSoft, LLC                               |
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
// |                                                                 |
// | The license that is bundled with this package is located in the |
// | file: /doc/manual/ch01-Introduction/license.html.               |
// | If not, see http://www.gnu.org/licenses/                        |
// +-----------------------------------------------------------------+
//  Path: /modules/inventory/custom/pages/main/filter_extra_actions.php
//
gen_pull_language('inventory', 'filter');

//
//The Product filter
//
//For finding products via creteria.
$allowed_actions = array('go_first','go_previous','go_next','go_last','search','go_page','filter','upload');

if(in_array($_POST['todo'],$allowed_actions )){
	$filter_criteria = Array(" = "," != "," LIKE "," NOT LIKE "," > "," < ");
	$x = 0;
	while (isset($_POST['filter_field'][$x])) {
		if(      $filter_criteria[$_POST['filter_criteria'][$x]] == " LIKE " || $_POST['filter_criteria'][$x] == FILTER_CONTAINS){
			$criteria[] = $_POST['filter_field'][$x] . ' Like "%'    . $_POST['filter_value'][$x] . '%" ';
			
		}elseif( $filter_criteria[$_POST['filter_criteria'][$x]] == " NOT LIKE "){
			$criteria[] = $_POST['filter_field'][$x] . ' Not Like "%' . $_POST['filter_value'][$x] . '%" ';
			
		}elseif( $filter_criteria[$_POST['filter_criteria'][$x]] == " = "  && $_POST['filter_value'][$x] == ''){
			$criteria[] = '(' . $_POST['filter_field'][$x] . $filter_criteria[$_POST['filter_criteria'][$x]] . ' "' . $_POST['filter_value'][$x] . '" or ' . $_POST['filter_field'][$x] . ' IS NULL ) ';
			
		}elseif( $filter_criteria[$_POST['filter_criteria'][$x]] == " != " && $_POST['filter_value'][$x] == ''){
			$criteria[] = '(' . $_POST['filter_field'][$x] . $filter_criteria[$_POST['filter_criteria'][$x]] . ' "' . $_POST['filter_value'][$x] . '" or ' . $_POST['filter_field'][$x] . ' IS NOT NULL ) ';
			
		}else{	
			$criteria[] = $_POST['filter_field'][$x] . $filter_criteria[$_POST['filter_criteria'][$x]]. ' "' . $_POST['filter_value'][$x] . '" ';
		}		
		$x++;
	}	
	$f0 = true;
}

$fields_array = array();
$query_raw = "select * from " . TABLE_EXTRA_FIELDS .' where module_id = "inventory" ORDER BY description ASC';
$query_result = $db->Execute($query_raw);
$i=0;
$posible_entry_type=array('drop_down','radio','multi_check_box');

$FirstValue = 'var FirstValue = new Array();' . chr(10);
$FirstId = 'var FirstId = new Array();' . chr(10);
$SecondField ='var SecondField = new Array();' . chr(10);
$SecondFieldValue='var SecondFieldValue = new Array();' . chr(10);
$SecondFieldId='var SecondFieldId = new Array();' . chr(10);
while (!$query_result->EOF) {
	if($query_result->fields['field_name']=='id')  $query_result->MoveNext();
	$fields_array['field_name'][$i]=$query_result->fields['field_name'];
	$FirstValue .= 'FirstValue[' . $i . '] = "' . $query_result->fields['description'] . '";' . chr(10);
	$FirstId 	.= 'FirstId[' . $i . '] = "' . $query_result->fields['field_name'] . '";' . chr(10);
	Switch($query_result->fields['field_name']){
	case 'vendor_id':
		$SecondField.= 'SecondField["' . $query_result->fields['field_name'] . '"] ="drop_down";' . chr(10);
		$contacts = gen_get_contact_array_by_type('v');
		//explode params and splits value form id
		$tempValue ='Array("'  ;
		$tempId ='Array("' ;
		$not_allowed = array("/","'",chr(34),);
		while ($contact = array_shift($contacts)) {
				$tempValue.= $contact['id'].'","';
				$tempId   .= str_replace($not_allowed, ' ', $contact['text']).'","';
		}
		$tempValue .='")' ;
		$tempId .='")' ;
		$SecondFieldValue.= 'SecondFieldValue["' . $query_result->fields['field_name'] . '"] ='. $tempValue . ';' . chr(10);
		$SecondFieldId.= 'SecondFieldId["' . $query_result->fields['field_name'] . '"] ='. $tempId . ';' . chr(10);
		break;
	case'inventory_type':
		$SecondField.= 'SecondField["' . $query_result->fields['field_name'] . '"] ="drop_down";' . chr(10);
		$tempValue ='Array("'  ;
		$tempId ='Array("' ;
		foreach ($inventory_types as $key => $value){
			$tempValue.= $key.'","';
			$tempId   .= $value.'","';
		}
		$tempValue .='")' ;
		$tempId .='")' ;
		$SecondFieldValue.= 'SecondFieldValue["' . $query_result->fields['field_name'] . '"] ='. $tempValue . ';' . chr(10);
		$SecondFieldId.= 'SecondFieldId["' . $query_result->fields['field_name'] . '"] ='. $tempId . ';' . chr(10);
		break;
	case'cost_method':
		$SecondField.= 'SecondField["' . $query_result->fields['field_name'] . '"] ="drop_down";' . chr(10);
		$tempValue ='Array("'  ;
		$tempId ='Array("' ;
		foreach ($cost_methods as $key => $value){
			$tempValue.= $key.'","';
			$tempId   .= $value.'","';
		}
		$tempValue .='")' ;
		$tempId .='")' ;
		$SecondFieldValue.= 'SecondFieldValue["' . $query_result->fields['field_name'] . '"] ='. $tempValue . ';' . chr(10);
		$SecondFieldId.= 'SecondFieldId["' . $query_result->fields['field_name'] . '"] ='. $tempId . ';' . chr(10);
		break;	
	case'inventory_type':
		$SecondField.= 'SecondField["' . $query_result->fields['field_name'] . '"] ="drop_down";' . chr(10);
		$tempValue ='Array("'.TEXT_NO.'","'.TEXT_YES.'")' ;
		$tempId ='Array("0","1")' ;
		$SecondFieldValue.= 'SecondFieldValue["' . $query_result->fields['field_name'] . '"] ='. $tempValue . ';' . chr(10);
		$SecondFieldId.= 'SecondFieldId["' . $query_result->fields['field_name'] . '"] ='. $tempId . ';' . chr(10);
		break;
	default:
		$SecondField.= 'SecondField["' . $query_result->fields['field_name'] . '"] ="'. $query_result->fields['entry_type'] . '";' . chr(10);
		if(in_array($query_result->fields['entry_type'],$posible_entry_type)){
			//explode params and splits value form id
			$tempValue ='Array("'  ;
			$tempId ='Array("' ;
		
			$params = unserialize($query_result->fields['params']);
			$choices = explode(',',$params['default']);
			while ($choice = array_shift($choices)) {
					$values = explode(':',$choice);
					$tempValue.= $values[0].'","';
					$tempId.= $values[1].'","';
			}
			$tempValue .='")' ;
			$tempId .='")' ;
			$SecondFieldValue.= 'SecondFieldValue["' . $query_result->fields['field_name'] . '"] ='. $tempValue . ';' . chr(10);
			$SecondFieldId.= 'SecondFieldId["' . $query_result->fields['field_name'] . '"] ='. $tempId . ';' . chr(10);
		}
	}	
	$i++;
    $query_result->MoveNext();
    }



?>
