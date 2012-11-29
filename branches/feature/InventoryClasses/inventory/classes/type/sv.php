<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class sv extends inventory {//Service
	public $title       			= INV_TYPES_SV;
	public $account_sales_income	= INV_SERVICE_DEFAULT_SALES;
	public $account_inventory_wage	= INV_SERVICE_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_SERVICE_DEFAULT_COS; 
		
}