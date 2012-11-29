<?php
require_once(DIR_FS_MODULES . 'inventory/classes/type/ms.php');
class mi extends ms { //Master Stock Sub Item. child of ma (master assembly) not sure maybe extend ma instead of inventory
	public $title 					= INV_TYPES_MI;
	public $master					= '';
/*	do not need the following because they will be set by parent.
 *  public $account_sales_income	= INV_ASSY_DEFAULT_SALES;
	public $account_inventory_wage	= INV_ASSY_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_ASSY_DEFAULT_COS;	
	
	*/
	function copy($id, $newSku) {
		global $messageStack;
		$messageStack->add(INV_ERROR_CANNOT_COPY, 'error');
		return false;
	}
	
	function check_remove($id){ // this is disabled in the form but just in case, error here as well
		global $messageStack;
		$messageStack->add_session('Master Stock Sub Items are not allowed to be deleted separately!','error');
		return false;
	}
	
	function get_ms_list(){
		global $db;
		$master = explode('-',$this->sku); 
		$this->master = $master[0];
		$result = $db->Execute("select * from " . TABLE_INVENTORY_MS_LIST . " where sku = '" . $this->master . "'");
	  	$this->ms_attr_0   = ($result->RecordCount() > 0) ? $result->fields['attr_0'] : '';
	  	$this->attr_name_0 = ($result->RecordCount() > 0) ? $result->fields['attr_name_0'] : '';
	  	$this->ms_attr_1   = ($result->RecordCount() > 0) ? $result->fields['attr_1'] : '';
	  	$this->attr_name_1 = ($result->RecordCount() > 0) ? $result->fields['attr_name_1'] : '';
		if ($this->ms_attr_0) {
			$temp = explode(',', $this->ms_attr_0);
			for ($i = 0; $i < count($temp); $i++) {
			  $code = substr($temp[$i], 0, strpos($temp[$i], ':'));
			  $desc = substr($temp[$i], strpos($temp[$i], ':') + 1);
			  $this->attr_array0[] = array('id' => $code . ':' . $desc, 'text' => $code . ' : ' . $desc);
			  $temp_ms0[$code] = $desc;
			}
		}
		if ($this->ms_attr_1) {
			$temp = explode(',', $this->ms_attr_1);
			for ($i = 0; $i < count($temp); $i++) {
			  $code = substr($temp[$i], 0, strpos($temp[$i], ':'));
			  $desc = substr($temp[$i], strpos($temp[$i], ':') + 1);
			  $this->attr_array1[] = array('id' => $code . ':' . $desc, 'text' => $code . ' : ' . $desc);
			  $temp_ms1[$code] = $desc;
			}
		}
		$result = $db->Execute("select * from " . TABLE_INVENTORY . " where sku like '" . $this->master . "-%' and inventory_type = 'mi' and sku<>'".$this->sku."'");
		while(!$result->EOF){
			$temp = explode('-',$result->fields['sku']); 
			$this->child_array[] = array(	'id'       	=> $result->fields['id'],
											'sku'      	=> $result->fields['sku'],
											'inactive' 	=> $result->fields['inactive'],
											'desc' 		=> $result->fields['description_short'],
											'0'			=> $temp_ms0[substr($temp[1],0,2)],
											'1'			=> (strlen($temp[1])>2)? $temp_ms1[substr($temp[1],2,4)] : '',
			);
			$result->MoveNext();
		}
	}
}