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
//  Path: /modules/rma/custom/pages/main/extra_js.php
//

// put some special pps calculations to set inventory stock levels
// this needs to be here because some values are not known until after pre_process
// start the extra javascript

?>
<script type="text/javascript">
<!--
<?php echo js_calendar_init($cal_init_date); ?>
<?php echo js_calendar_init($cal_recharge_date); ?>
<?php echo js_calendar_init($cal_test_date); ?>
<?php echo js_calendar_init($cal_contact_date); ?>
<?php echo js_calendar_init($cal_disp_date); ?>
// -->
</script>
