<?php
session_start();

if ( isset( $_SESSION['visitor_id']) && isset( $_SESSION['visitor_first_name']) ) {
	unset( $_SESSION['visitor_id']);
	unset( $_SESSION['visitor_first_name']);
	session_destroy();
	
	header( 'Location: index.php');
}
?>
