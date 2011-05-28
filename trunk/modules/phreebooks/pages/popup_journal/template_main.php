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
//  Path: /modules/phreebooks/pages/popup_journal/template_main.php
//

// start the form
echo html_form('popup_journal', FILENAME_DEFAULT, gen_get_all_get_params(array('action'))) . chr(10);

// include hidden fields
echo html_hidden_field('todo', '') . chr(10);

// customize the toolbar actions
$toolbar->icon_list['cancel']['params'] = 'onclick="self.close()"';
$toolbar->icon_list['open']['show']     = false;
$toolbar->icon_list['save']['show']     = false;
$toolbar->icon_list['delete']['show']   = false;
$toolbar->icon_list['print']['show']    = false;
// pull in extra toolbar overrides and additions
if (count($extra_toolbar_buttons) > 0) {
	foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
}
// add the help file index and build the toolbar
$toolbar->add_help('07.06.02');
if ($search_text) $toolbar->search_text = $search_text;
$toolbar->search_period = $acct_period;
echo $toolbar->build_toolbar($add_search = true, $add_periods = true); 

// Build the page
?>
<div class="pageHeading"><?php echo GEN_HEADING_PLEASE_SELECT; ?></div>
<div class="page_count_right"><?php echo $query_split->display_links($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['list']); ?></div>
<div class="page_count"><?php echo $query_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['list'], TEXT_DISPLAY_NUMBER . ORD_TEXT_2_WINDOW_TITLE); ?></div>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="dataTableHeadingRow"><?php echo $list_header; ?></tr>
<?php
	while (!$query_result->EOF) { ?>
  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="setReturnEntry(<?php echo $query_result->fields['id']; ?>)">
	<td class="dataTableContent"><?php echo gen_locale_date($query_result->fields['post_date']); ?></td>
	<?php if (ENABLE_MULTI_BRANCH) { ?>
	  <td class="dataTableContent"><?php echo $query_result->fields['store_id'] ? gen_get_contact_name($query_result->fields['store_id']) : COMPANY_ID; ?></td>
	<?php } ?>
	<td class="dataTableContent"><?php echo $query_result->fields['purchase_invoice_id']; ?></td>
	<td class="dataTableContent"><?php echo $currencies->format($query_result->fields['total_amount']); ?></td>
	<td class="dataTableContent"><?php echo substr(fetch_item_description($query_result->fields['id']), 0, 32); ?></td>
  </tr>
<?php
	  $query_result->MoveNext();
	}
?>
</table>
<div class="page_count_right"><?php echo $query_split->display_links($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['list']); ?></div>
<div class="page_count"><?php echo $query_split->display_count($query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['list'], TEXT_DISPLAY_NUMBER . ORD_TEXT_2_WINDOW_TITLE); ?></div>
</form>
