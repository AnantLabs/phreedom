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
//  Path: /modules/phreeform/pages/popup_phreefrom/tab_db_setup.php
//

?>
<div id="pf_db" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_DATABASE_SETUP; ?></h2>
  <h2 align="center"><?php echo TEXT_DATABASE_SETUP; ?></h2>
  <table width="60%" align="center">
	<tr>
	  <td>
		<table id="table_setup" width="100%" cellspacing="0" cellpadding="1"><thead>
		<tr>
		  <th colspan="20"><?php echo TEXT_DATABASE_TABLES; ?></th>
		</tr>
		<tr>
		  <th><?php echo TEXT_TABLE_NAME;     ?></th>
		  <th><?php echo TEXT_TABLE_CRITERIA; ?></th>
		  <th><?php echo TEXT_ACTION;         ?></th>
		</tr>
		<tr>
		  <td><?php 
		    echo html_pull_down_menu('table[]', $kTables, $report->tables[0]->tablename, 'onchange="fieldLoad()"');
		    echo html_hidden_field('table_crit[]');
		      ?>
		  </td>
		  <td colspan="2"><?php echo PHREEFORM_SPECIAL_REPORT . ' ' . html_input_field('special_class', $report->special_class); ?></td>
		</tr>
		</thead><tbody>
		<?php for ($i = 1; $i < sizeof($report->tables); $i++) { ?>
		  <tr>
			<td><?php echo html_pull_down_menu('table[]', $kTables, $report->tables[$i]->tablename, 'onchange="fieldLoad()"'); ?></td>
			<td><?php echo html_input_field('table_crit[]',         $report->tables[$i]->relationship,  'size="80"');  ?></td>
			<td align="right">
			  <?php 
		  	    echo html_icon('actions/view-fullscreen.png',   TEXT_MOVE,   'small', 'style="cursor:move"', '', '', 'move_table_' . $i) . chr(10);
				echo html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . TEXT_DELETE_ENTRY . '\')) rowAction(\'table_setup\', \'delete\')"'); 
			  ?>
			</td>
		  </tr>
		<?php } ?>
		</tbody></table>
	  </td>
	  <td valign="bottom"><?php echo html_icon('actions/list-add.png', TEXT_ADD, 'small', 'onclick="rowAction(\'table_setup\', \'add\')"'); ?></td>
	</tr>
	<tr>
	  <td colspan="3"><?php echo html_button_field('db_validate', TEXT_VALIDATE_RELATIONSHIPS, 'onclick="validateDB()"'); ?></td>
	</tr>
	<tr>
	  <td colspan="3"><?php echo PHREEFORM_DB_LINK_HELP; ?></td>
	</tr>
  </table>
</div>
