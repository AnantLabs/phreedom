<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title><?php echo TITLE_TOP_FRAME; ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_THEMES . 'css/stylesheet.css'; ?>" />
  <link rel="shortcut icon" type="image/ico" href="favicon.ico" />
  <script type="text/javascript">
    var icon_path        = '<?php echo DIR_WS_ICONS; ?>';
    var combo_image_on  = '<?php echo DIR_WS_ICONS . '16x16/phreebooks/pull_down_active.gif';   ?>';
    var combo_image_off = '<?php echo DIR_WS_ICONS . '16x16/phreebooks/pull_down_inactive.gif'; ?>';
    var pbBrowser       = (document.all) ? 'IE' : 'FF';
  </script>
  <script type="text/javascript" src="includes/common.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
  <?php require_once(DIR_FS_ADMIN  . 'themes/' . $_SESSION['theme'] . '/config.php'); ?>
  <?php require_once(DIR_FS_WORKING . 'pages/' . $page . '/js_include.php'); ?>

</head>

<body>
<ul class="tabset_tabs">
   <li><a href="#contents"<?php echo ($search_text == '') ? ' class="active"' : ''; ?>><?php echo HEADING_CONTENTS; ?></a></li>
   <li><a href="#index"><?php echo HEADING_INDEX; ?></a></li>
   <li><a href="#search"<?php echo ($search_text <> '') ? ' class="active"' : ''; ?>><?php echo TEXT_SEARCH; ?></a></li>
</ul>

<div id="contents" class="tabset_content">
	<h2 class="tabset_label"><?php echo HEADING_CONTENTS; ?></h2>
	<a href="javascript:Expand('doc');"><?php echo TEXT_EXPAND; ?></a> - <a href="javascript:Collapse('doc');"><?php echo TEXT_COLLAPSE; ?></a><br />
	<fieldset>
		<?php echo retrieve_toc(); ?>
	</fieldset>
</div>

<div id="index" class="tabset_content">
  <h2 class="tabset_label"><?php echo HEADING_INDEX; ?></h2>
  <a href="javascript:Expand('idx');"><?php echo TEXT_EXPAND; ?></a> - <a href="javascript:Collapse('idx');"><?php echo TEXT_COLLAPSE; ?></a><br />
  <fieldset><?php echo retrieve_index(); ?></fieldset>
</div>

<div id="search" class="tabset_content">
  <h2 class="tabset_label"><?php echo TEXT_SEARCH; ?></h2>
  <?php echo TEXT_KEYWORD; ?><br />
  <?php echo html_form('search_form', FILENAME_DEFAULT, 'module=phreehelp&amp;page=main&amp;fID=left'); ?>
    <?php echo html_input_field('search_text', $search_text); ?>
    <?php echo html_icon('actions/system-search.png', TEXT_SEARCH, 'small', 'style="cursor:pointer;" onclick="javascript:document.search_form.submit()"') . "\n"; ?>
  </form>
  <br />
  <?php if ($search_text) {
    echo TEXT_SEARCH_RESULTS . '<br />' . chr(10);
    echo '<fieldset>' . chr(10);
	echo search_results($search_text) . chr(10);
    echo '</fieldset>' . chr(10);
  } ?>
</div>

</body>
</html>
