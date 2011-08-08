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
//  Path: /install/pages/main/template_inspect.php
//

?>
<form name="install" id="install" action="index.php?action=inspect<?php echo $lang ? '&lang='.$lang : ''; ?>" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><p>&nbsp;</p><p>&nbsp;</p>
	  <table width="600" border="0" cellpadding="0" cellspacing="5" bgcolor="#CCCCCC">
        <tr>
          <td align="right"><img src="../modules/phreedom/images/phreesoft_logo.png" alt="Phreedom Small Business Toolkit" height="50" /></td>
        </tr>
<?php if ($error || $caution) { ?>
        <tr>
          <td bgcolor="#FFFFFF">
		    <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td><?php echo MSG_INSPECT_ERRORS; ?></td>
              </tr>
              <tr>
                <td><?php echo $messageStack->output(); ?></td>
              </tr>
            </table>
	      </td>
        </tr>
<?php } ?>
        <tr>
		  <td colspan="2" align="right">
			<?php echo html_submit_field('btn_recheck', TEXT_RECHECK); ?>
			<?php echo html_submit_field('btn_install', TEXT_INSTALL, $error ? 'disabled="disabled"' : ''); ?>
		  </td>
        </tr>
      </table>
    <p>&nbsp;</p></td>
  </tr>
</table>
</form>
