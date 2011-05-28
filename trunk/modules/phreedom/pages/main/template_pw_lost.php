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
//  Path: /modules/phreedom/pages/pw_lost/template_main.php
//

// start the form
echo html_form('pw_lost', FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;action=lost_pw&amp;req=pw_lost_sub') . chr(10);
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><p>&nbsp;</p><p>&nbsp;</p>
	  <table width="600" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
        <tr height="70">
          <td align="right"><img src="modules/phreedom/images/phreesoft_logo.png" alt="Phreedom Business Toolkit" height="50" /></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">
		    <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td colspan="2"><h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_PASSWORD_FORGOTTEN; ?></h2></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_ADMIN_EMAIL; ?></td>
                <td><?php echo html_input_field('admin_email', $_POST['admin_email'], 'size="80"'); ?></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_COMPANY; ?></td>
                <td><?php echo html_pull_down_menu('company', load_company_dropdown(), $admin_company); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right">
				  <?php echo html_submit_field('submit', TEXT_PASSWORD_FORGOTTEN) . '&nbsp;&nbsp;'; ?>
				</td>
              </tr>
            </table>
	      </td>
        </tr>
      </table>
    <p>&nbsp;</p></td>
  </tr>
</table>
</form>
