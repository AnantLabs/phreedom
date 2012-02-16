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
//  Path: /modules/contacts/classes/departments.php
//

class departments {
  function departments() {
  	$this->security_id = $_SESSION['admin_security'][SECURITY_ID_CONFIGURATION];
	$this->db_table    = TABLE_DEPARTMENTS;
	$this->help_path   = '07.07.04';
  }

  function btn_save($id = '') {
  	global $db, $messageStack;
	if ($this->security_id < 2) {
	  $messageStack->add_session(ERROR_NO_PERMISSION,'error');
	  return false;
	}
    if ( $_POST['subdepartment'] && !$_POST['primary_dept_id']) $_POST['subdepartment'] = '0';
    if (!$_POST['subdepartment']) $_POST['primary_dept_id'] = '';
    if ($_POST['primary_dept_id'] == $id) {
	  $messageStack->add_session(HR_DEPARTMENT_REF_ERROR,'error');
	  return false;
	}
	// OK to save
	$sql_data_array = array(
		'description_short'   => db_prepare_input($_POST['description_short']),
		'description'         => db_prepare_input($_POST['description']),
		'subdepartment'       => db_prepare_input($_POST['subdepartment']),
		'primary_dept_id'     => db_prepare_input($_POST['primary_dept_id']),
		'department_type'     => db_prepare_input($_POST['department_type']),
		'department_inactive' => db_prepare_input($_POST['department_inactive'] ? '1' : '0'));
    if ($id) {
	  db_perform($this->db_table, $sql_data_array, 'update', "id = '" . $id . "'");
      gen_add_audit_log(HR_LOG_DEPARTMENTS . TEXT_UPDATE, $id);
	} else  {
	  $sql_data_array['id'] = db_prepare_input($_POST['id']);
      db_perform($this->db_table, $sql_data_array);
	  gen_add_audit_log(HR_LOG_DEPARTMENTS . TEXT_ADD, $id);
	}
	return true;
  }

  function btn_delete($id = 0) {
  	global $db, $messageStack;
	if ($this->security_id < 4) {
		$messageStack->add_session(ERROR_NO_PERMISSION,'error');
		return false;
	}
	// error check
	// Departments have no pre-requisites to check prior to delete
	// OK to delete
	$db->Execute("delete from " . $this->db_table . " where id = '" . $id . "'");
	modify_account_history_records($id, $add_acct = false);
	gen_add_audit_log(HR_LOG_DEPARTMENTS . TEXT_DELETE, $id);
	return true;
  }

  function build_main_html() {
  	global $db, $messageStack;
    $content = array();
	$content['thead'] = array(
	  'value' => array(HR_ACCOUNT_ID, TEXT_DESCRIPTION, HR_HEADING_SUBACCOUNT, TEXT_INACTIVE, TEXT_ACTION),
	  'params'=> 'width="100%" cellspacing="0" cellpadding="1"',
	);
    $result = $db->Execute("select id, description_short, description, subdepartment, primary_dept_id, department_inactive from ".$this->db_table);
    $rowCnt = 0;
	while (!$result->EOF) {
	  $actions = '';
	  if ($this->security_id > 1) $actions .= html_icon('actions/edit-find-replace.png', TEXT_EDIT, 'small', 'onclick="loadPopUp(\'departments_edit\', \'' . $result->fields['id'] . '\')"') . chr(10);
	  if ($this->security_id > 3) $actions .= html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . HR_INFO_DELETE_INTRO . '\')) subjectDelete(\'departments\', ' . $result->fields['id'] . ')"') . chr(10);
	  $content['tbody'][$rowCnt] = array(
	    array('value' => htmlspecialchars($result->fields['description_short']),
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'departments_edit\',\''.$result->fields['id'].'\')"'),
		array('value' => htmlspecialchars($result->fields['description']), 
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'departments_edit\',\''.$result->fields['id'].'\')"'),
		array('value' => $result->fields['subdepartment'] ? TEXT_YES : TEXT_NO,
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'departments_edit\',\''.$result->fields['id'].'\')"'),
		array('value' => $result->fields['department_inactive'] ? TEXT_YES : TEXT_NO,
			  'params'=> 'style="cursor:pointer" onclick="loadPopUp(\'departments_edit\',\''.$result->fields['id'].'\')"'),
		array('value' => $actions,
			  'params'=> 'align="right"'),
	  );
      $result->MoveNext();
	  $rowCnt++;
    }
    return html_datatable('dept_table', $content);
  }

  function build_form_html($action, $id = '') {
    global $db;
    $sql = "select * from " . $this->db_table . " where id = '" . $id . "'";
    $result = $db->Execute($sql);
	if ($action == 'new') {
	  $cInfo = '';
	} else {
      $cInfo = new objectInfo($result->fields);
	}
	$output  = '<table style="border-collapse:collapse;margin-left:auto; margin-right:auto;">' . chr(10);
	$output .= '  <thead class="ui-widget-header">' . "\n";
	$output .= '  <tr>' . chr(10);
	$output .= '    <th colspan="2">' . ($action=='new' ? HR_INFO_NEW_ACCOUNT : HR_INFO_EDIT_ACCOUNT) . '</th>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  </thead>' . "\n";
	$output .= '  <tbody class="ui-widget-content">' . "\n";
    $output .= '  <tr>' . chr(10);
	$output .= '    <td colspan="2">' . ($action=='new' ? HR_INFO_INSERT_INTRO : HR_EDIT_INTRO) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . HR_ACCOUNT_ID . html_hidden_field('id', $cInfo->id) . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('description_short', $cInfo->description_short) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . TEXT_DESCRIPTION . '</td>' . chr(10);
	$output .= '    <td>' . html_input_field('description', $cInfo->description) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . HR_INFO_SUBACCOUNT . '</td>' . chr(10);
	$output .= '    <td>' . html_radio_field('subdepartment', '0', !$cInfo->subdepartment) . TEXT_NO . '<br />' . html_radio_field('subdepartment', '1', $cInfo->subdepartment) . HR_INFO_PRIMARY_ACCT_ID . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . HR_INFO_PRIMARY_ACCT_ID . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('primary_dept_id', gen_get_pull_down($this->db_table, false, '1', 'id', 'description_short'), $cInfo->primary_dept_id) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . HR_INFO_ACCOUNT_TYPE . '</td>' . chr(10);
	$output .= '    <td>' . html_pull_down_menu('department_type', gen_get_pull_down(TABLE_DEPT_TYPES, false, '1'), $cInfo->department_type) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  <tr>' . chr(10);
	$output .= '    <td>' . HR_INFO_ACCOUNT_INACTIVE . '</td>' . chr(10);
	$output .= '    <td>' . html_checkbox_field('department_inactive', '1', $cInfo->department_inactive ? true : false) . '</td>' . chr(10);
    $output .= '  </tr>' . chr(10);
	$output .= '  </tbody>' . "\n";
    $output .= '</table>' . chr(10);
    return $output;
  }
}
?>