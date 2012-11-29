<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class ai extends inventory {//Activity Item
	//cost_methods 'f' =( First-in, First-out),  'l' =( Last-in, First-out) , 'a' =( Average Costing )
	public $title					= INV_TYPES_AI;
	public $account_sales_income	= INV_ACTIVITY_DEFAULT_SALES;
	public $account_inventory_wage	= '';
	public $account_cost_of_sales	= '';	
	
}