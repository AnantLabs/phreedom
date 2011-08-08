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
//  Path: /modules/phreedom/pages/main/template_main.php
//

echo html_form('login', FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;action=validate', 'post', 'onsubmit="return submit_wait();"');
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
			    <td colspan="2"><?php echo $messageStack->output(); ?></td>
			  </tr>
              <tr>
                <td width="35%" class="main">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_NAME; ?></td>
                <td width="65%"><?php echo html_input_field('admin_name', $_POST['admin_name']); ?></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_PASS; ?></td>
                <td><?php echo html_password_field('admin_pass', ''); ?></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_COMPANY; ?></td>
                <td><?php echo html_pull_down_menu('company', load_company_dropdown(), $admin_company); ?></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_LANGUAGE; ?></td>
                <td><?php echo html_pull_down_menu('language', load_language_dropdown(), $admin_language); ?></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_LOGIN_THEME; ?></td>
                <td><?php echo html_pull_down_menu('theme', load_theme_dropdown(), $admin_theme); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right">
				  <div id="wait_msg" style="display: none;"><?php echo TEXT_FORM_PLEASE_WAIT; ?></div>
				  <?php echo html_submit_field('submit', TEXT_LOGIN_BUTTON); ?>
				</td>
              </tr>
              <tr>
                <td colspan="2"><?php echo '<a href="' . html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;req=pw_lost_req', 'SSL') . '">' . TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></td>
              </tr>
              <tr>
                <td colspan="2">
<?php echo TEXT_COPYRIGHT; ?> (c) 2008, 2009, 2010 <a href="http://www.PhreeSoft.com">PhreeSoft, LLC</a><br />
<?php echo sprintf(TEXT_COPYRIGHT_NOTICE, '<a href="' . DIR_WS_MODULES . 'phreedom/language/en_us/manual/ch01-Introduction/license.html">' . TEXT_HERE . '</a>'); ?>
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
