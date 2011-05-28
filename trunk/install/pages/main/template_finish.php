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
//  Path: /install/pages/main/template_finish.php
//

?>

<form name="install" id="install" action="index.php?action=open_company<?php echo $lang ? '&lang='.$lang : ''; ?>" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><p>&nbsp;</p><p>&nbsp;</p>
	  <table width="600" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
        <tr>
          <td align="right"><img src="../modules/phreedom/images/phreesoft_logo.png" alt="Phreedom Small Business Toolkit" height="50" /></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">
		    <table width="100%" border="0" cellspacing="0" cellpadding="1">
              <tr>
                <td colspan="2"><?php echo INTRO_FINISHED; ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo html_button_field('submit_form', TEXT_GO_TO_COMPANY, 'onclick="submit()"'); ?></td>
              </tr>
            </table>
	      </td>
        </tr>
      </table>
    <p>&nbsp;</p></td>
  </tr>
</table>
</form>
