<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class ia extends inventory { //Item Assembly Part. child of ma (master assembly) not sure maybe extend ma instead of inventory
	public $title 					= INV_TYPES_IA;
/*	do not need the following because they will be set by parent.
 *  public $account_sales_income	= INV_ASSY_DEFAULT_SALES;
	public $account_inventory_wage	= INV_ASSY_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_ASSY_DEFAULT_COS;	
	
	*/
}