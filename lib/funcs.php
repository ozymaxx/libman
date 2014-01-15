<?php
function display_page_head( $title) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"';
	echo '"http://www.w3.org/TR/xhtml1/dtd/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo '<head><title>'.$title.'</title><meta http-equiv="Content/Type" content="text/HTML; charset=utf-8;" />';
	echo '<link href="res/sty/makeup.css" rel="stylesheet" media="screen" type="text/css" />';
	echo '</head>';
}
?>
