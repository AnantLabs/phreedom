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
//  Path: /modules/phreedom/language/en_us/datepicker.php
//
// English (US) initialization for the jQuery UI date picker plugin.
// Written by Dave. This (English) is a sample file for translation and is loaded by default.
// Translated versions can be found at: http://jquery-ui.googlecode.com/svn/trunk/ui/i18n/
?>
<script type="text/javascript"> // jQuery UI datepicker Calendar translation
jQuery(function($){
	$.datepicker.regional['nl'] = {
		closeText: 'Sluiten',
		prevText: 'Vorige',
		nextText: 'Volgende',
		currentText: 'Vandaag',
		monthNames: ['Januari','Februari','Maart','April','Mei','Juni',
		'Juli','Augustus','September','Oktober','November','December'],
		monthNamesShort: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun',
		'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		dayNames: ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'],
		dayNamesShort: ['Zon', 'Maa', 'Din', 'Woe', 'Don', 'Vri', 'Zat'],
		dayNamesMin: ['Zo','Ma','Di','Wo','Do','Vr','Za'],
		weekHeader: 'Wk',
		dateFormat: '<?php echo DATE_FORMAT_CALENDAR; ?>',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['nl']);
});
</script>
