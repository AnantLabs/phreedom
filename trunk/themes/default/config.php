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
//  Path: /themes/default/config.php
//

// Set up the theme and conversion from defaults
define('THEME_NAME','Cool Blue (Default)');
?>

<?php if ($include_calendar) { // uses spiffyCal calendar ?>
  <link rel="stylesheet" type="text/css" href="themes/default/includes/spiffyCal/spiffyCal.css" />
<?php } ?>
<?php if ($include_header) { ?>
  <link rel="stylesheet" type="text/css" href="themes/default/css/ddsmoothmenu.css" />
  <script type="text/javascript" src="themes/default/ddsmoothmenu.js">
  /***********************************************
  * Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
  * This notice MUST stay intact for legal use
  * Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
  ***********************************************/
  </script>
  <script type="text/javascript">
    ddsmoothmenu.init({
	  mainmenuid: "smoothmenu1", //menu DIV id
	  orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	  classname: 'ddsmoothmenu', //class added to menu's outer DIV
	  //customtheme: ["#1c5a80", "#18374a"],
	  contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
    })
  </script>
  <script type="text/javascript"> // start the clock in the toolbar
    addLoadEvent(startClock);
    addUnloadEvent(endClock);
  </script>
<?php } ?>
<?php if ($include_tabs) { ?>
 <script type="text/javascript" src="themes/default/includes/tabtastic/tabs.js"></script>
<?php } ?>
<?php if ($include_calendar) {
// create the date format for the calendar (different from php) use only 'dd', 'MM' and 'yyyy'
$cal_format =  preg_replace(array('/m/', '/d/', '/Y/'), array('MM', 'dd', 'yyyy'), DATE_FORMAT);
define('DATE_FORMAT_CALENDAR', $cal_format);

// wrappers to initialize SpiffyCal, both html and javascript
function html_calendar_field($properties) {
  $output  = '<script type="text/javascript">' . chr(10);
  $output .= $properties['name'] . '.writeControl(); ';
  if (is_array($properties['params'])) foreach ($properties['params'] as $key => $value) {
    switch ($key) {
	  case 'align':    if ($value == 'left') $output .= $properties['name'] . '.displayLeft=true; '; break;
	  case 'onchange': $output .= $properties['name'] . '.JStoRunOnSelect="' . $value . '"; '; break;
	  case 'readonly': $output .= $properties['name'] . '.readonly=true; '; break;
	}
  }
  $output .= $properties['name'] . '.dateFormat="' . DATE_FORMAT_CALENDAR . '";' . chr(10);
  $output .= '</script>' . chr(10);
  return $output;
}

function js_calendar_init($properties) {
  $output  = 'var ' . $properties['name'] . ' = new ctlSpiffyCalendarBox("' . $properties['name'] . '","';
  $output .= $properties['form']      . '","';
  $output .= $properties['fieldname'] . '","';
  $output .= $properties['imagename'] . '","';
  $output .= $properties['default']   . '",scBTNMODE_CALBTN);' . chr(10);
  return $output;
}

?>
  <script type="text/javascript"> // Calendar translations
	var month_short_01 = '<?php echo TEXT_JAN; ?>';
	var month_short_02 = '<?php echo TEXT_FEB; ?>';
	var month_short_03 = '<?php echo TEXT_MAR; ?>';
	var month_short_04 = '<?php echo TEXT_APR; ?>';
	var month_short_05 = '<?php echo TEXT_MAY; ?>';
	var month_short_06 = '<?php echo TEXT_JUN; ?>';
	var month_short_07 = '<?php echo TEXT_JUL; ?>';
	var month_short_08 = '<?php echo TEXT_AUG; ?>';
	var month_short_09 = '<?php echo TEXT_SEP; ?>';
	var month_short_10 = '<?php echo TEXT_OCT; ?>';
	var month_short_11 = '<?php echo TEXT_NOV; ?>';
	var month_short_12 = '<?php echo TEXT_DEC; ?>';
	var day_short_1    = '<?php echo TEXT_SUN; ?>';
	var day_short_2    = '<?php echo TEXT_MON; ?>';
	var day_short_3    = '<?php echo TEXT_TUE; ?>';
	var day_short_4    = '<?php echo TEXT_WED; ?>';
	var day_short_5    = '<?php echo TEXT_THU; ?>';
	var day_short_6    = '<?php echo TEXT_FRI; ?>';
	var day_short_7    = '<?php echo TEXT_SAT; ?>';
  </script>
  <script type="text/javascript" src="themes/default/includes/spiffyCal/spiffyCal.js"></script>
<?php } /// end include_calendar ?>
