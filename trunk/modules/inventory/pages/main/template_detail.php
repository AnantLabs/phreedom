<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011 PhreeSoft, LLC             |
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
//  Path: /modules/inventory/pages/main/template_detail.php
//

// start the form
echo html_form('inventory', FILENAME_DEFAULT, gen_get_all_get_params(array('action', 'cID', 'sku', 'add')), 'post', 'enctype="multipart/form-data"');

// include hidden fields
echo html_hidden_field('todo', '') . chr(10);
echo html_hidden_field('rowSeq', $cInfo->id) . chr(10);
echo html_hidden_field('ms_attr_0', $cInfo->ms_attr_0) . chr(10);
echo html_hidden_field('ms_attr_1', $cInfo->ms_attr_1) . chr(10);

// customize the toolbar actions
if ($action == 'properties') {
  echo html_hidden_field('search_text', '') . chr(10);
  $toolbar->icon_list['cancel']['params'] = 'onclick="self.close()"';
  $toolbar->icon_list['open']['show']     = false;
  $toolbar->icon_list['delete']['show']   = false;
  $toolbar->icon_list['save']['show']     = false;
  $toolbar->icon_list['print']['show']    = false;
} else {
  $toolbar->icon_list['cancel']['params'] = 'onclick="location.href = \'' . html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action','page')), 'SSL') . '\'"';
  $toolbar->icon_list['open']['show']     = false;
  $toolbar->icon_list['delete']['show']   = false;
  if ($security_level > 2 || $first_entry) {
    $toolbar->icon_list['save']['params'] = 'onclick="submitToDo(\'save\')"';
  } else {
    $toolbar->icon_list['save']['show']   = false;
  }
  $toolbar->icon_list['print']['show']    = false;
  $toolbar->add_help('07.04.01.02');
}
echo $toolbar->build_toolbar(); 

?>
<div class="pageHeading"><?php echo MENU_HEADING_INVENTORY . ' - ' . TEXT_SKU . '# ' . $cInfo->sku . ' (' . $cInfo->description_short . ')'; ?></div>
<ul class="tabset_tabs">
<?php 
	echo '  <li><a href="#SYSTEM" class="active">' . TEXT_SYSTEM . '</a></li>' . chr(10);
	echo '  <li><a href="#HISTORY">' . TEXT_HISTORY . '</a></li>' . chr(10);
	if ($cInfo->inventory_type == 'as' || $cInfo->inventory_type == 'sa') echo '  <li><a href="#BOM">' . INV_BOM . '</a></li>' . chr(10);
	if ($cInfo->inventory_type == 'ms') echo '  <li><a href="#MSTR">' . INV_MS_ATTRIBUTES . '</a></li>' . chr(10);
	while (!$tab_list->EOF) {
		echo '  <li><a href="#cat_' . $tab_list->fields['id'] . '">' . $tab_list->fields['tab_name'] . '</a></li>' . chr(10);
		$tab_list->MoveNext();
	} 
	// pull in additional custom tabs
	if (isset($extra_inventory_tabs) && is_array($extra_inventory_tabs)) {
	  foreach ($extra_inventory_tabs as $tabs) {
		echo '  <li><a href="#' . $tabs['tab_id'] . '">' . $tabs['tab_title'] . '</a></li>' . chr(10);
	  }
	}
?>
</ul>

<!-- start the tabsets -->
<?php
  require (DIR_FS_WORKING . 'pages/main/template_tab_gen.php'); // general tab
  require (DIR_FS_WORKING . 'pages/main/template_tab_hist.php'); // history tab
if ($cInfo->inventory_type == 'as' || $cInfo->inventory_type == 'sa') {
  require (DIR_FS_WORKING . 'pages/main/template_tab_bom.php'); // bill of materials tab
}
if ($cInfo->inventory_type == 'ms') {
  require (DIR_FS_WORKING . 'pages/main/template_tab_ms.php'); // master stock tab
}

//********************************* List Custom Fields Here ***********************************
$tab_list->Move(0);
$tab_list->MoveNext();
while (!$tab_list->EOF) {
  echo '<div id="cat_' . $tab_list->fields['id'] . '" class="tabset_content">' . chr(10);
  echo '  <h2 class="tabset_label">' . $tab_list->fields['tab_name'] . '</h2>' . chr(10);
  echo '  <table border="0" cellspacing="2" cellpadding="2">' . chr(10);
  $field_list->Move(0);
  $field_list->MoveNext();
  while (!$field_list->EOF) {
	if ($tab_list->fields['id'] == $field_list->fields['tab_id']) {
	  echo xtra_field_build_entry($field_list->fields, $cInfo) . chr(10);
	}
	$field_list->MoveNext();
  }
  echo '  </table>' . chr(10);
  echo '</div>' . chr(10) . chr(10);
  $tab_list->MoveNext();
}

// pull in additional custom tabs
if (isset($extra_inventory_tabs) && is_array($extra_inventory_tabs)) {
  foreach ($extra_inventory_tabs as $tabs) {
    $file_path = DIR_FS_WORKING . 'custom/pages/main/' . $tabs['tab_filename'] . '.php';
    if (file_exists($file_path)) {
	  require($file_path);
	}
  }
}
?>
</form>
