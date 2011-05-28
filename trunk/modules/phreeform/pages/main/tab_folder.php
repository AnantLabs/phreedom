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
//  Path: /modules/phreeform/doc_ctl/pages/main/tab_folder.php
//

$fieldset_content = NULL;

// build the tab toolbar
$docbar = new toolbar;
$docbar->icon_list['cancel']['show']   = false;
$docbar->icon_list['open']['show']     = false;
$docbar->icon_list['save']['show']     = false;
$docbar->icon_list['print']['show']    = false;
if ($id) {
  $docbar->icon_list['delete']['params'] = 'onclick="if (confirm(\'' . PHREEFORM_DELETE_DIRECTORY . '\')) dirAction(\'delete\')"';
} else {
  $docbar->icon_list['delete']['show'] = false;
}
if ($id) $docbar->icon_list['go_up'] = array(
  'show'   => true, 
  'icon'   => 'actions/go-up.png',
  'params' => 'onclick="dirAction(\'go_up\')"',
  'text'   => 'Up', 
  'order'  => 2,
);
if ($action <> 'search') $fieldset_content .= $docbar->build_toolbar() . chr(10);

// build the table contents
$doc_cnt = 0;
$fieldset_content .= '<table border="0" width="100%" cellspacing="0" cellpadding="1">' . chr(10);
$fieldset_content .= '  <tr class="dataTableHeadingRow">' . $list_header . '</tr>' . chr(10);
while (!$query_result->EOF) {
  if (security_check($query_result->fields['security'])) {
	$folder = ($query_result->fields['doc_type'] == '0') ? true : false;
	$fieldset_content .= '  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . chr(10);
	$fieldset_content .= '	<td class="dataTableContent" onclick="fetch_doc(' . $query_result->fields['id'] . ')">' . html_icon(get_mime_image($query_result->fields['doc_ext'], $folder), '', 'small') . '</td>' . chr(10);
	$fieldset_content .= '	<td class="dataTableContent" onclick="fetch_doc(' . $query_result->fields['id'] . ')">' . $query_result->fields['doc_title'] . '</td>' . chr(10);
	$fieldset_content .= '	<td class="dataTableContent" align="right"> ' . '&nbsp;' . '</td>' . chr(10); // action space
	$fieldset_content .= '  </tr>' . chr(10);
	$doc_cnt++;
  }
  $query_result->MoveNext();
}
if ($doc_cnt > 0) {
  $fieldset_content .= '</table>' . chr(10);
  $fieldset_content .= '<div class="page_count_right">' . $query_split->display_links($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['list']) . '</div>' . chr(10);
  $fieldset_content .= '<div class="page_count">' . $query_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['list'], TEXT_DISPLAY_NUMBER . TEXT_FILES) . '</div>' . chr(10);
} else {
  $fieldset_content .= '  <tr class="dataTableRow">' . chr(10);
  $fieldset_content .= '	<td colspan="3" class="dataTableContent">' . TEXT_EMPTY_FOLDER . '</td>' . chr(10);
  $fieldset_content .= '  </tr>' . chr(10);
  $fieldset_content .= '</table>' . chr(10);
}
?>
