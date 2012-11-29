<?php
require_once(DIR_FS_MODULES . 'inventory/classes/inventory.php');
class sr extends inventory {//Serialized Item
	public $title       			= INV_TYPES_SR;
    public $serialize 				= 1;
    public $account_sales_income	= INV_SERIALIZE_DEFAULT_SALES;
	public $account_inventory_wage	= INV_SERIALIZE_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_SERIALIZE_DEFAULT_COS; 
	
}