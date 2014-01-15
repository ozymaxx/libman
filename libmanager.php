<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( !isset( $_SESSION['visitor_id']) ) {
	header( 'Location: index.php');
}
else {
	if ( isset( $_GET['lid']) ) {
		$conn = connectToSql();
		$libCheck = mysql_query( 'SELECT * FROM Visitor NATURAL JOIN Visits NATURAL JOIN Library WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn);
		if ( mysql_num_rows( $libCheck) == 1 ) {
			$libInfo = mysql_fetch_array( $libCheck);
			display_page_head( 'LibMan | '.$libInfo['visitor_first_name'].' in '.$libInfo['library_name']);
			echo '<body>';
			echo '<a href="logout.php">Logout</a><br />';
			echo '<a href="visitors.php">Back To Visitor Menu</a><br />';
			//get_status_bar( $_SESSION['visitor_id'], $_SESSION['visitor_first_name']);
			switch ( $libInfo['auth_level']) {
				case 'admin':
					echo 'You are the administrative staff in this library.';
					echo '<fieldset>';
					echo '<legend>Administrative Actions</legend>';
					echo '<a href="libraries.php?library_id='.$libInfo['library_id'].'">Library Shelf / Item / Visitor / Section Management</a><br />';
					echo '<a href="room.php?library_id='.$libInfo['library_id'].'">Room Management</a><br />';
					echo '<a href="actors.php?lid='.$libInfo['library_id'].'">Manage Actors</a><br />';
					echo '<a href="directors.php?lid='.$libInfo['library_id'].'">Manage Directors</a><br />';
					echo '<a href="authors.php?lid='.$libInfo['library_id'].'">Manage Authors</a><br />';
					echo '<a href="recorder.php?lid='.$libInfo['library_id'].'">Manage Recorders</a><br />';
					echo '<a href="policies.php?lid='.$libInfo['library_id'].'">Set / Set Up Time Policies</a><br />';
					echo '<a href="statistics.php">See Statistics</a><br />';
					echo '</fieldset>';
					echo '<fieldset>';
					echo '<legend>Visitor Actions</legend>';
					echo '<a href="display.php?lid='.$libInfo['library_id'].'">Browse Library</a><br />';
					echo '<a href="reservations.php?lid='.$libInfo['library_id'].'">My Reservations</a><br />';
					echo '<a href="borrows.php?lid='.$libInfo['library_id'].'">Items Borrowed From Library</a><br />';
					echo '<a href="visitroom.php?library_id='.$libInfo['library_id'].'">Visit or dump a room</a><br />';
					echo '</fieldset>';
				break;
				
				case 'only':
					echo 'You are only the visitor of this library.';
					echo '<legend>Visitor Actions</legend>';
					echo '<a href="display.php?lid='.$libInfo['library_id'].'">Browse Library</a><br />';
					echo '<a href="reservations.php?lid='.$libInfo['library_id'].'">My Reservations</a><br />';
					echo '<a href="borrows.php?lid='.$libInfo['library_id'].'">Items Borrowed From Library</a><br />';
					echo '<a href="visitroom.php?library_id='.$libInfo['library_id'].'">Visit or dump a room</a><br />';
					echo '</fieldset>';
				break;
			}
			echo '</body>';
		}
		else {
			header( 'Location: index.php');
		}
	}
	else {
		header( 'Location: index.php');
	}
}
?>
