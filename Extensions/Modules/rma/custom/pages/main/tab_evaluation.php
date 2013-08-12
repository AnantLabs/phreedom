<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |

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
//  Path: /modules/inventory/custom/pages/main/template_tab_accessory.php
//

// start the accessory tab html
?>
<div id="tab_evaluation">
 <fieldset>
  <legend>Intial Test</legend>
	 <table class="ui-widget" style="border-style:none;">
	  <tbody class="ui-widget-content">
		<tr>
		  <td colspan="2"><?php echo 'Open circuit voltage (OCV) as received. Total, per pack and per battery.'; ?></td>
		  <td align="right"><?php echo 'Tested By: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_init_by', $user_choices, $pps['pps_init_by']); ?></td>
		</tr>
		<tr>
		  <td><?php echo 'Initial Check '; ?></td>
		  <td><?php echo html_input_field('pps_init_results', $pps['pps_init_results'], 'size="60"'); ?></td>
		  <td align="right"><?php echo 'Date: '; ?></td>
		  <td><?php echo html_calendar_field($cal_init_date); ?></td>
		</tr>
	  </tbody>
	 </table>
   </fieldset>
 <fieldset>
  <legend>Recharge/Preparation for Testing</legend> 
	 <table class="ui-widget" style="border-style:none;">
	  <tbody class="ui-widget-content">
		<tr>
		  <td><?php echo 'Recharge required before test?'; ?></td>
		  <td><?php echo TEXT_YES. html_radio_field('pps_recharge_req', '1', $pps['pps_recharge_req']==1 ? true : false) . ' '; ?>
		      <?php echo TEXT_NO . html_radio_field('pps_recharge_req', '0', $pps['pps_recharge_req']==0 ? true : false); ?></td>
		  <td align="right"><?php echo 'Recharged By: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_recharge_by', $user_choices, $pps['pps_recharge_by']); ?></td>
		</tr>
		<tr>
		  <td><?php echo 'If recharge was required, was it successful?'; ?></td>
		  <td><?php echo TEXT_YES. html_radio_field('pps_recharge_success', '1', $pps['pps_recharge_success']==1 ? true : false) . ' '; ?>
		      <?php echo TEXT_NO . html_radio_field('pps_recharge_success', '0', $pps['pps_recharge_success']==0 ? true : false); ?></td>
		  <td align="right"><?php echo 'Date: '; ?></td>
		  <td><?php echo html_calendar_field($cal_recharge_date); ?></td>
		</tr>
		<tr>
		  <td><?php echo 'If recharge was required and not successful, why?'; ?></td>
		  <td><?php echo html_pull_down_menu('pps_recharge_failed', gen_build_pull_down($pps_recharge_codes), $pps['pps_recharge_failed']); ?></td>
		</tr>
	  </tbody>
	 </table>
   </fieldset>
 <fieldset>
  <legend>Load Testing</legend>
	 <table class="ui-widget" style="border-style:none;">
	  <tbody class="ui-widget-content">
		<tr>
		  <td><?php echo 'Describe tests performed and results/observations.'; ?></td>
		  <td align="right"><?php echo 'Tested By: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_test_by', $user_choices, $pps['pps_test_by']); ?></td>
		</tr>
		<tr>
	      <td><?php echo html_textarea_field('pps_test_notes', 60, 3, $pps['pps_test_notes'], '', true); ?></td>
		  <td align="right" valign="top"><?php echo 'Date: '; ?></td>
		  <td><?php echo html_calendar_field($cal_test_date); ?></td>
		</tr>
		<tr>
		</tr>
	  </tbody>
	 </table>
   </fieldset>
 <fieldset>
  <legend>Final Results</legend>
	 <table class="ui-widget" style="border-style:none;">
	  <tbody class="ui-widget-content">
		<tr>
		  <td colspan="3"><?php echo 'Closing comments and method of disposition.'; ?></td>
		</tr>
		<tr>
		  <td><?php echo 'Customer Notification:' . html_pull_down_menu('pps_contact_code', gen_build_pull_down($pps_contact_codes), $pps['pps_contact_code']); ?></td>
		  <td align="right"><?php echo 'Contacted By: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_contact_by', $user_choices, $pps['pps_contact_by']); ?></td>
		</tr>
		<tr>
		  <td colspan="2" align="right"><?php echo 'Date: '; ?></td>
		  <td><?php echo html_calendar_field($cal_contact_date); ?></td>
		</tr>
		<tr>
	      <td rowspan="3"><?php echo html_textarea_field('pps_contact_notes', 60, 3, $pps['pps_contact_notes'], '', true); ?></td>
		  <td><?php echo 'Battery Disposition: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_disp_code', gen_build_pull_down($pps_disp_codes), $pps['pps_disp_code']); ?></td>
		</tr>
		<tr>
		  <td align="right"><?php echo 'By: '; ?></td>
		  <td><?php echo html_pull_down_menu('pps_disp_by', $user_choices, $pps['pps_disp_by']); ?></td>
		</tr>
		<tr>
		  <td align="right"><?php echo 'Date: '; ?></td>
		  <td><?php echo html_calendar_field($cal_disp_date); ?></td>
		</tr>
	  </tbody>
	 </table>
   </fieldset>
</div>
