<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( isset( $_SESSION['visitor_id']) && isset( $_GET['lid']) ) {
	$conn = connectToSql();
	$libCheck = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
	if ( mysql_num_rows( $libCheck) == 1 ) {
		$libInfo = mysql_fetch_array( $libCheck);
		display_page_head( 'LibMan | Your Reservations in '.$libInfo['library_name']);
		echo '<body>';
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="success">The item has been released back to the library!</p>';
				break;
				
				case '2':
					echo '<p class="warning">Error while releasing the item, please contact the administrator!</p>';
				break;
				
				case '3':
					echo '<p class="success">The item has been reserved in the library, the end date for reservation is 1 week far from that moment.</p>';
				break;
				
				case '4':
					echo '<p class="warning">Error while reserving the item, please contact the administrator!</p>';
				break;
			}
		}
		echo '<fieldset>';
		echo '<legend>The Items You have reserved (not released)</legend>';
		$items = mysql_query( 'SELECT DISTINCT item_id, item_name, shelf_type, reserve_start_time, reserve_end_supposed_time FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE library_id='.mysql_real_escape_string( $_GET['lid']).' AND reserved_visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).' ORDER BY publication_date DESC;') or die( mysql_error() );
		if ( mysql_num_rows( $items) > 0 ) {
			echo '<table>';
			echo '<tr><th>Item Name</th><th>Type</th><th>Reserve Started</th><th>Reserve Ended</th><th>Actions</th></tr>';
			while ( $item = mysql_fetch_array( $items) ) {
				echo '<tr><td><a href="display.php?lid='.$_GET['lid'].'&dispid='.strip_tags($item['item_id']).'">'.strip_tags($item['item_name']).'</a></td><td>'.strip_tags( $item['shelf_type']).'</td><td>'.strip_tags( $item['reserve_start_time']).'</td><td>'.strip_tags( $item['reserve_end_supposed_time']).'</td><td><a href="action.php?todo=reserveback&lid='.$_GET['lid'].'&itemid='.$item['item_id'].'">Release</a></td></tr>';
			}
			echo '</table>';
		}
		else {
			echo '<p class="info">There is no item being reserved in the library.</p>';
		}
		echo '</fieldset>';
		echo '</body>';
	}
	else {
		header( 'Location: index.php');
	}
}
else {
	header( 'Location: index.php');
}
?>
