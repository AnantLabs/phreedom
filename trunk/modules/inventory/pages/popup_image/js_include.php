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
//  Path: /modules/inventory/pages/popup_image/js_include.php
//

?>
<script type="text/javascript">
<!--
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.

function init() {
  resize();
}

function check_form() {
  return true;
}

// Insert other page specific functions here.
var arrTemp = self.location.href.split("?");
var picUrl  = (arrTemp.length > 0) ? arrTemp[1] : "";
var NS      = (navigator.appName=="Netscape") ? true : false;
function resize() {
  iWidth  = (NS) ? window.innerWidth  : document.body.clientWidth;
  iHeight = (NS) ? window.innerHeight : document.body.clientHeight;
  iWidth  = document.getElementById('popup_image').width  - iWidth;
  iHeight = document.getElementById('popup_image').height - iHeight;
  window.resizeBy(iWidth, iHeight);
  self.focus();
};

// -->
</script>