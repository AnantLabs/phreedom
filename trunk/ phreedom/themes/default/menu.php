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
//  Path: /themes/default/menu.php
//

if ($include_header) { 
?>
<!-- Menu Bar -->
<div class="headerBar">
  <div class="headerBarContent" style="float:right;"><a href="<?php echo html_href_link(FILENAME_DEFAULT, 'module=phreedom&amp;page=main&amp;action=logout', 'SSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_LOGOFF; ?></a>&nbsp;</div>
  <div class="headerBarContent" style="float:right;"><a href="<?php echo html_href_link(FILENAME_DEFAULT, 'module=phreehelp&amp;page=main', 'SSL'); ?>" class="headerLink" target="_blank"><?php echo TEXT_HELP; ?></a>&nbsp;|&nbsp;</div>
  <div class="headerBarContent" style="float:right;"><a href="<?php echo html_href_link(FILENAME_DEFAULT, '', 'SSL'); ?>" class="headerLink"><?php echo HEADER_TITLE_TOP; ?></a>&nbsp;|&nbsp;</div>
  <?php // start the left heading fields ?>
  <?php if (ENABLE_ENCRYPTION && strlen($_SESSION['admin_encrypt']) > 0) {
  	echo '<div class="headerBarContent" style="float:left;">' . html_icon('emblems/emblem-readonly.png', TEXT_ENCRYPTION_ENABLED, 'small') . '</div>';
  } ?>
  <div class="headerBarContent" style="float:left;"><?php echo COMPANY_NAME; ?></div>
  <div class="headerBarContent" style="float:left;"><?php echo ' | '; ?></div>
  <div class="headerBarContent" style="float:left;"><?php echo TEXT_ACCOUNTING_PERIOD . ': ' . CURRENT_ACCOUNTING_PERIOD; ?></div>
  <div class="headerBarContent" style="float:left;"><?php echo ' | '; ?></div>
  <div class="headerBarContent" id="rtClock" ><?php echo '&nbsp;' . date(DATE_FORMAT, time()); ?></div>
</div>

<!-- Pull Down Menu -->
<div id="smoothmenu1" class="ddsmoothmenu">
<ul>
<?php
if (is_array($pb_headings)) {
  ksort($pb_headings); // sorts the category headings with included extra modules
  foreach ($pb_headings as $box) {
    $sorted_menu  = array();
	$just_reports = true;
    foreach ($menu as $item)  {
	  if (isset($item['heading']) && !$item['hidden']) {
	    if ($item['heading'] == $box['text'] && $_SESSION['admin_security'][$item['security_id']] > 0) {
//echo 'text = ' . $item['text'] . '<br>';
		  $sorted_menu['text'][]    = $item['text'];
		  $sorted_menu['heading'][] = $item['heading'];
		  $sorted_menu['rank'][]    = $item['rank'];
		  $sorted_menu['link'][]    = $item['link'];
		  if ($item['text'] <> TEXT_REPORTS) $just_reports = false;
	    }
	  }
    }
    if (is_array($sorted_menu['rank']) && !$just_reports) {
	  $result = array_multisort(
		  $sorted_menu['rank'], SORT_ASC, SORT_NUMERIC, 
		  $sorted_menu['text'], SORT_ASC, SORT_STRING, 
		  $sorted_menu['link'], SORT_ASC, SORT_STRING);
	  if ($result) {
	    echo '<li><a href="' . $box['link'] . '">' . $box['text'] . '</a>' . chr(10);
	    echo '  <ul>' . chr(10);
	    foreach ($sorted_menu['text'] as $key => $item) {
	      echo '    <li><a href="' . $sorted_menu['link'][$key] . '">' . $item . '</a></li>' . chr(10);
	    }
	    echo '  </ul>' . chr(10);
	    echo '  </li>' . chr(10);
	  } else {
	    die('Error in multi-sort in header_navigation.php');
	  }
    }
  }
}

?>
</ul>
<br style="clear: left" />
</div>
<?php } // end if ($include_header) ?>

<?php if ($include_calendar) echo '<div id="spiffycalendar" class="text"></div>'.  chr(10); ?>
