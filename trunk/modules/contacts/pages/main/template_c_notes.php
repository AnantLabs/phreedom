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
//  Path: /modules/contacts/pages/main/template_c_notes.php
//
?>
<div id="cat_notes" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_NOTES; ?></h2>
  <fieldset class="formAreaTitle">
    <legend><?php echo TEXT_NOTES; ?></legend>
    <table border="0" width="100%" cellspacing="6" cellpadding="0">
	  <tr>
	    <td><?php echo html_textarea_field('cm_notes', 120, 30, $cInfo->cm_notes, $parameters = ''); ?></td>
	  </tr>
	</table>
  </fieldset>
</div>