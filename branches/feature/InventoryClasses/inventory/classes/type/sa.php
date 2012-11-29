<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class sa extends inventory {//Serialized Assembly
	public $title 					= INV_TYPES_SA;
	public $serialize 				= 1;
    public $account_sales_income	= INV_SERIALIZE_DEFAULT_SALES;
	public $account_inventory_wage	= INV_SERIALIZE_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_SERIALIZE_DEFAULT_COS; 
	public $bom_list 				= array();
	public $allow_edit_bom			= true;
	
	function __construct(){
		parent::__construct();
		$this->tab_list[] = array('file'=>'template_tab_bom',	'tag'=>'bom',    'order'=>30, 'text'=>INV_BOM);
	}
	
	function get_item_by_id($id){
		parent::get_item_by_id($id);
		$this->get_bom_list();
	}
	
	function get_item_by_sku($sku){
		parent::get_item_by_sku($sku);
		$this->get_bom_list();
	}
	
	function get_bom_list(){
		global $db;
		$result = $db->Execute("select l.id, l.sku, l.description, l.qty, i.item_cost, i.full_price from " . TABLE_INVENTORY_ASSY_LIST . " l join " . TABLE_INVENTORY . " i on l.sku = i.sku where l.ref_id = " . $this->id . " order by l.id");
		while (!$result->EOF) {
	  		$this->bom_list[] = $result->fields;
	  		$result->MoveNext();
		}
		$this->allow_edit_bom = ($this->last_journal_date == '0000-00-00 00:00:00');
	}
	
	function save(){
		global $db, $messageStack;
		parent::save();	  	
		$result = $db->Execute("select last_journal_date from " . TABLE_INVENTORY . " where id = " . $this->id);
	  	if ($result->fields['last_journal_date'] == '0000-00-00 00:00:00') { // only update if no posting has been performed
			$bom_array = array();
			for($x=0; $x<count($_POST['assy_sku']); $x++) {
		  		$assy_sku = db_prepare_input($_POST['assy_sku'][$x]);
		  		$assy_qty = $currencies->clean_value(db_prepare_input($_POST['assy_qty'][$x]));
		  		$result = $db->Execute("select item_cost, full_price from " . TABLE_INVENTORY . " where sku = " . $assy_sku );
		  		if ($result->RecordCount() > 0 && $assy_qty > 0) { // error check sku is valid and qty > 0
					$bom_array[] = array(
			  			'ref_id'      => $this->id,
			  			'sku'         => $assy_sku,
			  			'description' => db_prepare_input($_POST['assy_desc'][$x]),
			  			'qty'         => $assy_qty,
					);
					$this->bom_list[] = array(
			  			'ref_id'      => $this->id,
			  			'sku'         => $assy_sku,
			  			'description' => db_prepare_input($_POST['assy_desc'][$x]),
			  			'qty'         => $assy_qty,
						'item_cost'   => $result->fields['item_cost'],
						'full_price'  => $result->fields['full_price'],
					);
		  		} elseif ($assy_sku <> '' || $assy_qty < 0) { // show error, bad sku, negative quantity, skip the blank lines
					$messageStack->add(INV_ERROR_BAD_SKU . $assy_sku, 'error');
		  		}
			}
	  		$result = $db->Execute("delete from " . TABLE_INVENTORY_ASSY_LIST . " where ref_id = " . $id);
			while ($list_array = array_shift($bom_array)) {
		  		db_perform(TABLE_INVENTORY_ASSY_LIST, $list_array, 'insert');
			}
	  	}
	  	return true;
	}
}