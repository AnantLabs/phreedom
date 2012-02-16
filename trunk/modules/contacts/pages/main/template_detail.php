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
//  Path: /modules/contacts/pages/main/template_detail.php
//
echo html_form('contacts', FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'post', 'enctype="multipart/form-data"', true) . chr(10);
// include hidden fields
echo html_hidden_field('todo',        '') . chr(10);
echo html_hidden_field('f0',         $f0) . chr(10);
echo html_hidden_field('id',  $cInfo->id) . chr(10);
echo html_hidden_field('rowSeq',      '') . chr(10);
echo html_hidden_field('del_crm_note','') . chr(10);
echo html_hidden_field('payment_id',  '') . chr(10);
// customize the toolbar actions
if ($action == 'properties') {
  $toolbar->icon_list['cancel']['params'] = 'onclick="self.close()"';
  $toolbar->icon_list['save']['show']     = false;
} else {
  $toolbar->icon_list['cancel']['params'] = 'onclick="location.href = \'' . html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL') . '\'"';
  if ((!$cInfo->id && $security_level < 2) || ($cInfo->id && $security_level < 3)) {
    $toolbar->icon_list['save']['show']   = false;
  } else {
    $toolbar->icon_list['save']['params'] = 'onclick="submitToDo(\'save\')"';
  }
}
$toolbar->icon_list['open']['show']       = false;
$toolbar->icon_list['delete']['show']     = false;
$toolbar->icon_list['print']['show']      = false;

// pull in extra toolbar overrides and additions
if (count($extra_toolbar_buttons) > 0) {
  foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
}

// add the help file index and build the toolbar
switch ($type) {
  case 'c': $toolbar->add_help('07.03.02.02'); break;
  case 'v': $toolbar->add_help('07.02.02.02'); break;
  case 'e': $toolbar->add_help('07.07.01.02'); break;
  case 'b': $toolbar->add_help('07.08.04');    break;
  default:
}
if ($search_text) $toolbar->search_text = $search_text;
echo $toolbar->build_toolbar(); 

// Build the page
$extra_tab_li   = '';
$extra_tab_html = '';
while (!$xtra_tab_list->EOF) {
  $found_one = false;
  $field_list->Move(0);
  $field_list->MoveNext();
  $xtra_header  = '<div id="tab_' . $xtra_tab_list->fields['id'] . '">' . chr(10);
  $xtra_header .= '  <table>' . chr(10);
  while (!$field_list->EOF) {
	if ($xtra_tab_list->fields['id'] == $field_list->fields['tab_id']) {
	  $xtra_params = unserialize($field_list->fields['params']);
	  $temp = explode(':',$xtra_params['contact_type']);
	  while ($value = array_shift($temp)){
	  	if (substr($value, 0, 1) == $type) {
		    $xtra_header .= xtra_field_build_entry($field_list->fields, $cInfo) . chr(10);
			$found_one = true;
	  	}
	  }
	}
	$field_list->MoveNext();
  }
  $xtra_header .= '  </table>';
  $xtra_header .= '</div>' . chr(10);
  if ($found_one) {
    $extra_tab_li   .= '  <li><a href="#tab_' . $xtra_tab_list->fields['id'] . '">' . $xtra_tab_list->fields['tab_name'] . '</a></li>' . chr(10);
    $extra_tab_html .= $xtra_header;
  }
  $xtra_tab_list->MoveNext();
} 

$custom_path = DIR_FS_MODULES . 'contacts/custom/pages/main/extra_tabs.php';
if (file_exists($custom_path)) { include($custom_path); }

function tab_sort($a, $b) {
  if ($a['order'] == $b['order']) return 0;
  return ($a['order'] > $b['order']) ? 1 : -1;
}
usort($tab_list, 'tab_sort');

?>
<h1><?php echo PAGE_TITLE; ?></h1>
<div id="detailtabs">
<ul>
<?php // build the tab list's
  $set_default = false;
  foreach ($tab_list as $value) {
  	echo add_tab_list('tab_'.$value['tag'],  $value['text']);
	$set_default = true;
  }
  echo $extra_tab_li . chr(10); // user added extra tabs
?>
</ul>
<?php
foreach ($tab_list as $value) {
  if (file_exists(DIR_FS_WORKING . 'custom/pages/main/template_' . $type . '_' . $value['tag'] . '.php')) {
	include(DIR_FS_WORKING . 'custom/pages/main/template_' . $type . '_' . $value['tag'] . '.php');
  } else {
	include(DIR_FS_WORKING . 'pages/main/template_' . $type . '_' . $value['tag'] . '.php');
  }
}
// pull in additional custom tabs
if (isset($extra_contact_tabs) && is_array($extra_contact_tabs)) {
  foreach ($extra_contact_tabs as $tabs) {
    $file_path = DIR_FS_WORKING . 'custom/pages/main/' . $tabs['tab_filename'] . '.php';
    if (file_exists($file_path)) { require($file_path);	}
  }
}
echo $extra_tab_html;
?>
</div>
</form>