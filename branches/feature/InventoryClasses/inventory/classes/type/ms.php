<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class ms extends inventory {//Master Stock Item
	public $title       	= INV_TYPES_MS;
	public $attr_array0 	= array();
	public $attr_array1 	= array();
	public $ms_attr_0		= '';
	public $ms_attr_1		= '';
	public $attr_name_0		= '';
	public $attr_name_1		= '';
	public $child_array 	= array();
	
	function __construct(){
		parent::__construct();
		$this->tab_list[] = array('file'=>'template_tab_ms',	'tag'=>'master',    'order'=>30, 'text'=>INV_MS_ATTRIBUTES);
	}
	
	function get_item_by_id($id){
		parent::get_item_by_id($id);
		$this->get_ms_list();
	}
	
	function get_item_by_sku($sku){
		parent::get_item_by_sku($sku);
		$this->get_ms_list();
	}
	
	function get_ms_list(){
		global $db;
		$result = $db->Execute("select * from " . TABLE_INVENTORY_MS_LIST . " where sku = '" . $this->sku . "'");
	  	$this->ms_attr_0   = ($result->RecordCount() > 0) ? $result->fields['attr_0'] 		: '';
	  	$this->attr_name_0 = ($result->RecordCount() > 0) ? $result->fields['attr_name_0'] 	: '';
	  	$this->ms_attr_1   = ($result->RecordCount() > 0) ? $result->fields['attr_1'] 		: '';
	  	$this->attr_name_1 = ($result->RecordCount() > 0) ? $result->fields['attr_name_1'] 	: '';
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
		$result = $db->Execute("select * from " . TABLE_INVENTORY . " where sku like '" . $this->sku . "-%' and inventory_type = 'mi'");
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

	function check_remove($id){
		global $messageStack, $db;
		if(isset($id))$this->get_item_by_id($id);
		// check to see if there is inventory history remaining, if so don't allow delete
		$result = $db->Execute("select id from " . TABLE_INVENTORY_HISTORY . " where sku = '" . $this->sku . "' and remaining > 0");
		if ($result->RecordCount() > 0) {
		 	$messageStack->add(INV_ERROR_DELETE_HISTORY_EXISTS, 'error');
		 	return false;
		}
		// check to see if this item is part of an assembly
		$result = $db->Execute("select id from " . TABLE_INVENTORY_ASSY_LIST . " where sku = '" . sku . "'");
		if ($result->RecordCount() > 0) {
	  		$messageStack->add(INV_ERROR_DELETE_ASSEMBLY_PART, 'error');
	  		return false;
		}
		$result = $db->Execute( "select id from " . TABLE_JOURNAL_ITEM . " where sku like '" . $sku . "-%' limit 1");
		if ($result->Recordcount() > 0) {
			$messageStack->add(INV_ERROR_CANNOT_DELETE, 'error');
	  		return false;	
		}
		$this->remove();
	  	return true;
		
	}
	
	function remove(){
		global $db;
		parent::remove();
		$db->Execute("delete from " . TABLE_INVENTORY_MS_LIST . " where sku = '" . $this->sku . "'");
		$db->Execute("delete from " . TABLE_INVENTORY . " where sku like '" . $this->sku . "-%'");
	}
	
	function save(){
		require_once(DIR_FS_MODULES . 'inventory/classes/type/mi.php');
		global $db, $messageStack, $security_level;
		If (!parent::save()) return false;
		$attributes = array(
			'attr_name_0' => $this->attr_name_0,
			'ms_attr_0'   => substr($this->ms_attr_0, 0, -1),
			'attr_name_1' => $this->attr_name_1,
			'ms_attr_1'   => substr($this->ms_attr_1, 0, -1),
	  	);		
		// 	split attributes
		$attr0 = explode(',', $this->ms_attr_0);
		$attr1 = explode(',', $this->ms_attr_1);
		if (!count($attr0)) return true; // no attributes, nothing to do
		// build skus
		$sku_list = array();
		for ($i = 0; $i < count($attr0); $i++) {
			$temp = explode(':', $attr0[$i]);
			$idx0 = $temp[0];
			if (count($attr1)) {
				for ($j = 0; $j < count($attr1); $j++) {
					$temp = explode(':', $attr1[$j]);
					$idx1 = $temp[0];
					$sku_list[] = $sql_data_array['sku'] . '-' . $idx0 . $idx1;
				}
			} else {
				$sku_list[] = $sql_data_array['sku'] . '-' . $idx0;
			}
		}
		// either update, delete or insert sub skus depending on sku list
		$result = $db->Execute("select sku from " . TABLE_INVENTORY . " where inventory_type = 'mi' and sku like '" . $this->sku . "-%'");
		$existing_sku_list = array();
		while (!$result->EOF) {
			$existing_sku_list[] = $result->fields['sku'];
			$result->MoveNext();
		}
		$delete_list = array_diff($existing_sku_list, $sku_list);
		$update_list = array_intersect($existing_sku_list, $sku_list);
		$insert_list = array_diff($sku_list, $update_list);
		foreach($update_list as $sku) {
			$sql_data_array['sku'] = $sku;
			db_perform(TABLE_INVENTORY, $sql_data_array, 'update', "sku = '" . $sku . "'");
		}
		foreach($insert_list as $sku) {
			$sql_data_array['sku'] = $sku;
			db_perform(TABLE_INVENTORY, $sql_data_array, 'insert');
		}
		
		if (count($delete_list) && $security_level < 4){
			$messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  		return false;
		}
		$sql_data_array['inventory_type'] = 'mi';
		foreach($delete_list as $sku) {
			if($this->mi_check_remove($sku)){
				$result = $db->Execute("delete from " . TABLE_INVENTORY . " where sku = '" . $sku . "'");
			}
		}
		
		// update/insert into inventory_ms_list table
		$result = $db->Execute("select id from " . TABLE_INVENTORY_MS_LIST . " where sku = '" . $this->sku . "'");
		$exists = $result->RecordCount();
		$data_array = array(
			'sku'         => $this->sku,
			'attr_0'      => $attributes['ms_attr_0'],
			'attr_name_0' => $attributes['attr_name_0'],
			'attr_1'      => $attributes['ms_attr_1'],
			'attr_name_1' => $attributes['attr_name_1']);
		if ($exists) {
			db_perform(TABLE_INVENTORY_MS_LIST, $data_array, 'update', "id = " . $result->fields['id']);
		} else {
			db_perform(TABLE_INVENTORY_MS_LIST, $data_array, 'insert');
		}
		$this->get_ms_list();
		return true;
	}
	
	function mi_check_remove($sku) {
		global $messageStack, $db;
		// check to see if there is inventory history remaining, if so don't allow delete
		$result = $db->Execute("select id from " . TABLE_INVENTORY_HISTORY . " where sku = '" . $sku . "' and remaining > 0");
		if ($result->RecordCount() > 0) {
		 	$messageStack->add(INV_ERROR_DELETE_HISTORY_EXISTS, 'error');
		 	return false;
		}
		// check to see if this item is part of an assembly
		$result = $db->Execute("select id from " . TABLE_INVENTORY_ASSY_LIST . " where sku = '" . $sku . "'");
		if ($result->RecordCount() > 0) {
	  		$messageStack->add(INV_ERROR_DELETE_ASSEMBLY_PART, 'error');
	  		return false;
		}
		$result = $db->Execute( "select id from " . TABLE_JOURNAL_ITEM . " where sku = '" . $sku . "' limit 1");
		if ($result->Recordcount() > 0) {
			$messageStack->add(INV_ERROR_CANNOT_DELETE, 'error');
	  		return false;	
		}
	  	return true;
		
	}
}