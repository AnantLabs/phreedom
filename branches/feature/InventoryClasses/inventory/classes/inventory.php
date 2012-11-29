<?php
//require_once(DIR_FS_WORKING . 'functions/inventory.php');
class inventory {
	
	public $help_path   			= '07.04.01.02';
	public $title       			= '';
	public $auto_field    			= '';
	public $tab_list    			= array(); 
	public $account_sales_income	= INV_STOCK_DEFAULT_SALES;
	public $account_inventory_wage	= INV_STOCK_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_STOCK_DEFAULT_COS;
	public $item_taxable			= INVENTORY_DEFAULT_TAX;
	public $purch_taxable			= INVENTORY_DEFAULT_PURCH_TAX;
	public $serialize 				= 0;
	public $store_stock 			= array();
	
	public function __construct(){
		global $db;
		foreach ($_POST as $key => $value) $this->$key = $value;
		$this->inventory_type = get_called_class();
		$this->creation_date = date('Y-m-d');
	  	$this->last_update = date('Y-m-d');
		$this->tab_list[] = array('file'=>'template_tab_gen',	'tag'=>'general',   'order'=>10, 'text'=>TEXT_SYSTEM);
		$this->tab_list[] = array('file'=>'template_tab_hist',	'tag'=>'history',   'order'=>20, 'text'=>TEXT_HISTORY);
		if($this->auto_field){
			$result = $db->Execute("select ".$this->auto_field." from ".TABLE_CURRENT_STATUS);
        	$this->new_sku = $result->fields[$this->auto_field];
		}
	}
	
	function get_item_by_id($id){
		global $db;
		$result = $db->Execute("select * from " . TABLE_INVENTORY . " where id = '" . $id  . "'");
		foreach ($result->fields as $key => $value) $this->$key = $value;
		if (ENABLE_MULTI_BRANCH) $this->get_stock_per_branch();
	}
	
	function get_item_by_sku($sku){
		global $db;
		$result = $db->Execute("select * from " . TABLE_INVENTORY . " where sku = '" . $sku  . "'");
		foreach ($result->fields as $key => $value) $this->$key = $value;
		if (ENABLE_MULTI_BRANCH) $this->get_stock_per_branch();
	}
	
	function get_stock_per_branch(){
		global $db;
		$sql = " select short_name, primary_name from " . TABLE_CONTACTS . " c join " . TABLE_ADDRESS_BOOK . " a on c.id = a.ref_id where c.type = 'b'";
	  	$result = $db->Execute($sql);
	  	$this->store_stock[] = array('store' => COMPANY_ID, 'order'=> 1, 'qty' => load_store_stock($this->sku, 0));
	  	while (!$result->EOF) {
			$this->store_stock[] = array(
		  		'store' => $result->fields['primary_name'], 
				'order' => $result->fields['short_name'],
		  		'qty'   => load_store_stock($this->sku, $result->fields['id']) );
			$result->MoveNext();
	  	}
	}
	
	//this is to check if you are allowed to create a new product
	function check_create_new() {
		global $messageStack;
		if (!$this->sku) $this->sku = $this->next_sku;
		if (!$this->sku) {
		  	$messageStack->add(INV_ERROR_SKU_BLANK, 'error');
		  	return false;
		}
		if (gen_validate_sku($this->sku)) {
		  	$messageStack->add(INV_ERROR_DUPLICATE_SKU, 'error');
			return false;
		}
		return $this->create_new();
	}
	
	//this is the general create new inventory item
	function create_new() {
		$sql_data_array = array(
	  		'sku'						=> $this->sku,
	  		'inventory_type'			=> $this->inventory_type,
	  		'cost_method'				=> $this->cost_method,
	  		'creation_date'				=> $this->creation_date,
	  		'last_update'				=> $this->last_update,
	  		'item_taxable'				=> $this->item_taxable,
	  		'purch_taxable'				=> $this->purch_taxable,
			'account_sales_income'   	=> $this->account_sales_income,
		    'account_inventory_wage'	=> $this->account_inventory_wage,
			'account_cost_of_sales'  	=> $this->account_cost_of_sales,
			'serialize'					=> $this->serialize,
			);
		db_perform(TABLE_INVENTORY, $sql_data_array, 'insert');
		$this->id = db_insert_id();
		if (ENABLE_MULTI_BRANCH) $this->get_stock_per_branch();
		gen_add_audit_log(INV_LOG_INVENTORY . TEXT_ADD, TEXT_TYPE . ': ' . $this->inventory_type . ' - ' . $this->sku );
		return true;
	}
	
	//this is to copy a product
	function copy($id, $newSku) {
		global $db, $messageStack;
		if (!$newSku) $newSku = $this->next_sku;
		if (!$newSku) {
		  	$messageStack->add(INV_ERROR_SKU_BLANK, 'error');
		  	return false;
		}
		if (gen_validate_sku($newSku)) {
		  	$messageStack->add(INV_ERROR_DUPLICATE_SKU, 'error');
			return false;
		}
		if(isset($id))$this->get_item_by_id($id);
		$old_sku						= $this->sku;
		$this->id           			= '';
		$this->sku						= $newSku ;
		$this->last_journal_date 		= '';
		$this->item_cost 				= '';
		$this->upc_code 				= '';
		$this->image_with_path 			= '';
		$this->quantity_on_hand 		= '';
		$this->quantity_on_order 		= '';
		$this->quantity_on_sales_order	= '';
		$this->creation_date 			= date('Y-m-d H:i:s');
		$this->last_update 				= date('Y-m-d H:i:s');
		  
		$this->save();
		$result = $db->Execute("select price_sheet_id, price_levels from " . TABLE_INVENTORY_SPECIAL_PRICES . " where inventory_id = " . $id);
		while(!$result->EOF) {
	  		$output_array = array(
				'inventory_id'   => $this->id,
				'price_sheet_id' => $result->fields['price_sheet_id'],
				'price_levels'   => $result->fields['price_levels'],
	  		);
	  		db_perform(TABLE_INVENTORY_SPECIAL_PRICES, $output_array, 'insert');
	  		$result->MoveNext();
		}
		gen_add_audit_log(INV_LOG_INVENTORY . TEXT_COPY, $old_sku . ' => ' . $this->sku);
		return true;
	}
	
	/*	
 	* this function is for renaming
 	*/
	
	function rename($id, $newSku){
		global $db, $messageStack;
		if (!$newSku) $newSku = $this->next_sku;
		if (!$newSku) {
		  	$messageStack->add(INV_ERROR_SKU_BLANK, 'error');
		  	return false;
		}
		if (gen_validate_sku($newSku)) {
		  	$messageStack->add(INV_ERROR_DUPLICATE_SKU, 'error');
			return false;
		}
		if(isset($id))$this->get_item_by_id($id); 
		$sku_list = array($this->sku);
		if ($this->inventory_type == 'ms') { // build list of sku's to rename (without changing contents)
	  		$result = $db->Execute("select sku from " . TABLE_INVENTORY . " where sku like '" . $this->sku . "-%'");
	  		while(!$result->EOF) {
				$sku_list[] = $result->fields['sku'];
				$result->MoveNext();
	  		}
		}
		// start transaction (needs to all work or reset to avoid unsyncing tables)
		$db->transStart();
		// rename the afffected tables
		for ($i = 0; $i < count($sku_list); $i++) {
	  		$new_sku = str_replace($this->sku, $newSku, $sku_list[$i], $count = 1);
	  		$result = $db->Execute("update " . TABLE_INVENTORY .           " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  		$result = $db->Execute("update " . TABLE_INVENTORY_ASSY_LIST . " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  		$result = $db->Execute("update " . TABLE_INVENTORY_COGS_OWED . " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  		$result = $db->Execute("update " . TABLE_INVENTORY_HISTORY .   " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  		$result = $db->Execute("update " . TABLE_INVENTORY_MS_LIST .   " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
	  		$result = $db->Execute("update " . TABLE_JOURNAL_ITEM .        " set sku = '" . $new_sku . "' where sku = '" . $sku_list[$i] . "'");
		}
		$db->transCommit();
	}
	
	//this is to check if you are allowed to remove
	function check_remove($id) {
		global $messageStack, $db;
		if(isset($id))$this->get_item_by_id($id);
		// check to see if there is inventory history remaining, if so don't allow delete
		$result = $db->Execute("select id from " . TABLE_INVENTORY_HISTORY . " where sku = '" . $this->sku . "' and remaining > 0");
		if ($result->RecordCount() > 0) {
		 	$messageStack->add(INV_ERROR_DELETE_HISTORY_EXISTS, 'error');
		 	return false;
		}
		// check to see if this item is part of an assembly
		$result = $db->Execute("select id from " . TABLE_INVENTORY_ASSY_LIST . " where sku = '" . $this->sku . "'");
		if ($result->RecordCount() > 0) {
	  		$messageStack->add(INV_ERROR_DELETE_ASSEMBLY_PART, 'error');
	  		return false;
		}
		$result = $db->Execute( "select id from " . TABLE_JOURNAL_ITEM . " where sku = '" . $this->sku . "' limit 1");
		if ($result->Recordcount() > 0) {
			$messageStack->add(INV_ERROR_CANNOT_DELETE, 'error');
	  		return false;	
		}
		$this->remove();
	  	return true;
		
	}
	
	// this is the general remove function 
	// the function check_remove calls this function. 
	function remove(){
		global $db;
		$db->Execute("delete from " . TABLE_INVENTORY . " where id = " . $this->id);
	  	if ($this->image_with_path) { // delete image
			$file_path = DIR_FS_MY_FILES . $_SESSION['company'] . '/inventory/images/';
			if (file_exists($file_path . $this->image_with_path)) unlink ($file_path . $this->image_with_path);
	  	}
	  	$db->Execute("delete from " . TABLE_INVENTORY_SPECIAL_PRICES . " where inventory_id = '" . $this->id . "'");
		gen_add_audit_log(INV_LOG_INVENTORY . TEXT_DELETE, $this->sku);
	}
	
	// this is the general save function.
	function save() {
		global $currencies, $fields;
		if (substr($this->inventory_path, 0, 1) == '/')  $this->inventory_path = substr($this->inventory_path, 1); // remove leading '/' if there
		if (substr($this->inventory_path, -1, 1) == '/') $this->inventory_path = substr($this->inventory_path, 0, strlen($this->inventory_path)-1); // remove trailing '/' if there
	    $sql_data_array = $fields->what_to_save();
		$sql_data_array['last_update'] = date('Y-m-d H-i-s');
		// special cases for checkboxes of system fields (don't return a POST value if unchecked)
//		$remove_image = $this->remove_image == '1' ? true : false;
		unset($this->remove_image); // this is not a db field, just an action
		$sql_data_array['inactive']   = ($sql_data_array['inactive'] == '1' ? '1' : '0');
		// special cases for monetary values in system fields
		$sql_data_array['full_price'] = $currencies->clean_value($sql_data_array['full_price']);
		if ($_SESSION['admin_security'][SECURITY_ID_PURCHASE_INVENTORY] > 1) {
	 		$sql_data_array['item_cost']  = $currencies->clean_value($sql_data_array['item_cost']);
		}else{
//			unset items that belong to the purchase part otherwise they will overwrite current values with null?
		}
		$file_path = DIR_FS_MY_FILES . $_SESSION['company'] . '/inventory/images';
		if ($this->remove_image == '1') { // update the image with relative path
	  		if ($this->image_with_path && file_exists($file_path . '/' . $this->image_with_path)) unlink ($file_path . '/' . $this->image_with_path);
	  		$this->image_with_path = '';
	  		$sql_data_array['image_with_path'] = ''; 
		}
		if (!$error && is_uploaded_file($_FILES['inventory_image']['tmp_name'])) {
	  		if ($this->image_with_path && file_exists($file_path . '/' . $this->image_with_path)) unlink ($file_path . '/' . $this->image_with_path);
      		$this->inventory_path = str_replace('\\', '/', $this->inventory_path);
			// strip beginning and trailing slashes if present
	  		if (substr($this->inventory_path, -1, 1) == '/') $this->inventory_path = substr($this->inventory_path, 0, -1);
	  		if (substr($this->inventory_path, 0, 1) == '/') $this->inventory_path = substr($this->inventory_path, 1);
	  		if ($this->inventory_path) $file_path .= '/' . $this->inventory_path;
	  		$temp_file_name = $_FILES['inventory_image']['tmp_name'];
	  		$file_name = $_FILES['inventory_image']['name'];
	  		if (!validate_path($file_path)) {
				$messageStack->add(INV_IMAGE_PATH_ERROR, 'error');
				return false;
	  		} elseif (!validate_upload('inventory_image', 'image', 'jpg')) {
				$messageStack->add(INV_IMAGE_FILE_TYPE_ERROR, 'error');
				return false;
	  		} else { // passed all test, write file
				if (!copy($temp_file_name, $file_path . '/' . $file_name)) {
		  			$messageStack->add(INV_IMAGE_FILE_WRITE_ERROR, 'error');
		  			return false;
				} else {
		  			$this->image_with_path = ($this->inventory_path ? ($this->inventory_path . '/') : '') . $file_name;
		  			$sql_data_array['image_with_path'] = $this->image_with_path; // update the image with relative path
				}
	  		}
		}
		
	  	if (is_array($extra_sql_data_array)) $sql_data_array = array_merge($sql_data_array, $extra_sql_data_array);
		If ($this->id != ''){
			db_perform(TABLE_INVENTORY, $sql_data_array, 'update', "id = " . $this->id);
			gen_add_audit_log(INV_LOG_INVENTORY . TEXT_UPDATE, $this->sku . ' - ' . $sql_data_array['description_short']);
		}else{
			db_perform(TABLE_INVENTORY, $sql_data_array, 'insert');
			$this->id = db_insert_id();
		}
		return true;
	}
	
	function __destruct(){
		if(DEBUG) print_r($this);
	}
}