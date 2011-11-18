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
//  Path: /modules/contacts/pages/main/template_i_notes.php
//

$cal_i_note = array(
  'name'      => 'crmDate'.$j,
  'form'      => 'contacts',
  'fieldname' => 'crm_date_'.$j,
  'imagename' => 'btn_i_note'.$j,
  'default'   => gen_locale_date(date('Y-m-d')),
  'params'    => array('align' => 'left'),
);
?>
<script type="text/javascript"><?php echo js_calendar_init($cal_i_note); ?></script>

<div id="tab_notes">
  <fieldset>
  <legend><?php echo TEXT_NOTES; ?></legend>
  <table><tr>
	<td id="contactList">
	  <table id="crm_notes">
        <tr>
          <th><?php echo html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small'); ?></th>
          <th><?php echo TEXT_DATE; ?></th>
          <th><?php echo TEXT_ACTION; ?></th>
          <th><?php echo TEXT_NOTES; ?></th>
        </tr>
<?php 
if ($cInfo->crm_notes) {
  $i = 1;
  foreach ($cInfo->crm_notes as $key => $value) { 
?>
		<tr id="trn_<?php echo $i; ?>">
		  <td align="center">
		    <?php echo $security_level < 4 ? '&nbsp;' : html_icon('emblems/emblem-unreadable.png', TEXT_DELETE, 'small', 'onclick="if (confirm(\'' . CRM_ROW_DELETE_ALERT . '\')) removeCRMRow(' . $i . ');"'); ?>
			<?php echo html_hidden_field('im_note_id_'.$i, $value['log_id']); ?>
		  </td>
		  <td><?php echo gen_locale_date($value['log_date']); ?></td>
		  <td><?php echo html_input_field('crm_act_' . $i, htmlspecialchars($crm_actions[$value['action']]), 'readonly="readonly"'); ?></td>
	      <td><?php echo html_textarea_field('crm_note_' . $i, 50, 1, htmlspecialchars($value['notes']), 'readonly="readonly"'); ?></td>
		</tr>
<?php
    $i++;
  }
} ?>
     </table>
	</td>
  </tr>
  <tr>
    <td align="left"><?php echo html_icon('actions/list-add.png', TEXT_ADD, 'medium', 'onclick="addCRMRow()"'); ?></td>
  </tr></table>
<?php // display the hidden fields that are not used in this rendition of the form
$hidden_fields .= '  <script type="text/javascript">addCRMRow();</script>' . chr(10);
echo $hidden_fields;
?>
  </fieldset>
</div>