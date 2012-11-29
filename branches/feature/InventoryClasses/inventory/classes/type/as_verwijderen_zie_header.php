<?php
/*
 * er moet nog een upgrade functie uitgevoerd worden waarin het inventory_type as hernoemd word naar ma
 */
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class inventory_as extends inventory { //Item Assembly
	public $title 					= INV_TYPES_AS;
	public $account_sales_income	= INV_ASSY_DEFAULT_SALES;
	public $account_inventory_wage	= INV_ASSY_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_ASSY_DEFAULT_COS;	
	
	function __construct(){
		parent::__construct();
		$this->tab_list[] = array('file'=>'tab_bom',	'tag'=>'bom',    'order'=>30, 'text'=>INV_BOM);
	}
}