<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011, 2012 PhreeSoft, LLC       |
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
// +-----------------------------------------------------------------+
//  Path: /modules/phreepos/classes/tills.php
//

class tills {
    public $db_table    = TABLE_PHREEPOS_TILLS;
    public $help_path   = '';
    public $error       = false;
    
    public function __construct(){
         $this->security_id = $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION];
         foreach ($_POST as $key => $value) $this->$key = $value;
         $this->id = isset($_POST['sID'])? $_POST['sID'] : $_GET['sID'];
         $this->store_ids = gen_get_store_ids();
    }

  function btn_save($id = '') {
  	global $db, $messageStack;
	if ($this->security_id < 2) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		return false;
	}
	if ($this->gl_acct_id ==''){
		$messageStack->add(GL_SELECT_STD_CHART,'error');
		return false;
	}
	$sql_data_array = array(
		'description' 		   => $this->description,
		'store_id'    		   => $this->store_id,
		'gl_acct_id'  		   => $this->gl_acct_id,
		'rounding_gl_acct_id'  => $this->rounding_gl_acct_id,
	);
    if ($id) {
	  db_perform($this->db_table, $sql_data_array, 'update', "till_id = '" . $id . "'");
	  gen_add_audit_log(SETUP_TAX_AUTHS_LOG . TEXT_UPDATE, $this->description);
	} else  {
      db_perform($this->db_table, $sql_data_array);
	  gen_add_audit_log(SETUP_TAX_AUTHS_LOG . TEXT_ADD, $this->description);
	}
	return true;
  }

  function btn_delete($id = 0) {
  	global $db, $messageStack;
	if ($this->security_id < 4) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		return false;
	}
	// Don't allow delete if there is account activity for this account
	$sql = "select max(debit_amount) as debit, max(credit_amount) as credit, max(beginning_balance) as beg_bal 
		from " . TABLE_CHART_OF_ACCOUNTS_HISTORY . " where account_id = '" . $this->gl_acct_id . "'";
	$result = $db->Execute($sql);
	if ($result->fields['debit'] <> 0 || $result->fields['credit'] <> 0 || $result->fields['beg_bal'] <> 0) {
	  $messageStack->add(GL_ERROR_CANT_DELETE, 'error');
	  return false;
	}
	// OK to delete
	$result = $db->Execute("select description from " . $this->db_table . " where till_id = '" . $id . "'");
	$db->Execute("delete from " . $this->db_table . " where till_id = '" . $id . "'");
	gen_add_audit_log(SETUP_TAX_AUTHS_LOG . TEXT_DELETE, $result->fields['description']);
	return true;
  }

  function build_main_html() {
  	global $db, $messageStack;
    $content = array();
	$content['thead'] = array(
	  'value' => array(TEXT_DESCRIPTION, GEN_STORE_ID, TEXT_GL_ACCOUNT, TEXT_ACTION),
	  'params'=> 'width="100%" cellspacing="0" cellpadding="1"',
	);
    $result = $db->Execute("select * from " . $this->db_table );
    $rowCnt = 0;
	while (!$result->EOF) {
	  $actions = '';
	  if ($this->security_id > 1) $actions .= html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 'onclick="loadPopUp(\''.get_called_class().'_edit\', ' . $result->fields['till_id'] . ')"') . chr(10);
	  if ($this->security_id > 3) $actions .= html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . SETUP_TILL_DELETE_INTRO . '\')) subjectDelete(\''.get_called_class().'\', ' . $result->fields['till_id'] . ')"') . chr(10);
	  $content['tbody'][$rowCnt] = array(
	    array('value' => htmlspecialchars($result->fields['description']),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\''.get_called_class().'_edit\',\''.$result->fields['till_id'].'\')"'),
		array('value' => htmlspecialchars($result->fields['store_id']), 
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\''.get_called_class().'_edit\',\''.$result->fields['till_id'].'\')"'),
		array('value' => gen_get_type_description(TABLE_CHART_OF_ACCOUNTS, $result->fields['gl_acct_id']),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\''.get_called_class().'_edit\',\''.$result->fields['till_id'].'\')"'),
		array('value' => $actions,
			  'params'=> 'align="right"'),
	  );
      $result->MoveNext();
	  $rowCnt++;
    }
    return html_datatable(''.get_called_class().'_table', $content);
  }

  function build_form_html($action, $id = '') {
    global $db;
    if ($action <> 'new' && $this->error == false) {
        $sql = "select * from " . $this->db_table . " where till_id = " . $id;
        $result = $db->Execute($sql);
        foreach ($result->fields as $key => $value) $this->$key = $value;
	}
	$output  = '<table style="border-collapse:collapse;margin-left:auto; margin-right:auto;">' . chr(10);
	$output .= '  <thead class="ui-widget-header">' . "\n";
	$output .= '  <tr>' . chr(10);
	$output .= '    <th colspan="2">' . ($action=='new' ? TEXT_ENTER_NEW_TILL : TEXT_EDIT_TILL) . '</th>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  </thead>' . "\n";
	$output .= '  <tbody class="ui-widget-content">' . "\n";
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . TEXT_DESCRIPTION . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('description', $this->description, 'size="16" maxlength="15"') . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . GEN_STORE_ID . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('store_id', $this->store_ids, $this->store_id) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . TEXT_GL_ACCOUNT . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('gl_acct_id', gen_coa_pull_down(SHOW_FULL_GL_NAMES, true, true, false, $restrict_types = array(0)), $this->gl_acct_id) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . TEXT_GL_ACCOUNT_ROUNDING . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('rounding_gl_acct_id', gen_coa_pull_down(SHOW_FULL_GL_NAMES, true, true, false, $restrict_types = array(30)), $this->rounding_gl_acct_id) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
    $output .= '  </tbody>' . "\n";
    $output .= '</table>' . chr(10);
    return $output;
  }
// functions for template main  
  function showDropDown(){
  	global $db;
  	foreach ($this->store_ids as $store){
  		$temp[]= $store['id'];
  	}
  	$sql = "select till_id, description from " . $this->db_table . " where store_id in (" . implode(',', $temp) . ")";
    $result = $db->Execute($sql);    
    if ($result->RecordCount()== 1) {
    	return false;
    }else{
    	return true;
    }
  }
  
  function default_till(){
  	global $db;
  	$sql = "select till_id from " . $this->db_table . " where store_id = '" . $_SESSION['admin_prefs']['def_store_id']."'";
    $result = $db->Execute($sql);
    return $result->fields['till_id'];
  }
  
  function till_array(){
  	global $db;
  	foreach ($this->store_ids as $store){
  		$temp[]= $store['id'];
  	}
  	$sql = "select till_id, description from " . $this->db_table . " where store_id in (" . implode(',', $temp) . ")";
    $result = $db->Execute($sql);
    while(!$result->EOF){
    	$result_array[] = array('id' => $result->fields['till_id'], 'text' => $result->fields['description']);
    	$result->MoveNext();
    }
    return $result_array;
  }
  
  function get_till_info($till_id){
  	global $db;
  	$sql = "select * from " . $this->db_table . " where till_id = " . $till_id;
    $result = $db->Execute($sql);
    foreach ($result->fields as $key => $value) $this->$key = $value;
  }
  
  function __destruct(){
  	//print_r($this);
  }
}
?>