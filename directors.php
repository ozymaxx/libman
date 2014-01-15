<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( !isset( $_SESSION['visitor_id']) && !isset( $_GET['lid']) ) {
	header( 'Location: index.php');
}
else {
	$conn = connectToSql();
	$libCheck = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library WHERE library_id='.mysql_real_escape_string( $_GET['lid']).' AND auth_level="admin" AND visitor_id='.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
	if ( mysql_num_rows( $libCheck) ) {
		$lib = mysql_fetch_array( $libCheck);
		display_page_head( 'LibMan | Manage Director in Library '.strip_tags($lib['library_name']));
		echo '<body>';
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="success">The director and its videos have successfully been removed from the system!</p>';
				break;
				
				case '2':
					echo '<p class="warning">Error removing the director, contact the administrator!</p>';
				break;
			}
		}
		echo '<fieldset>';
		echo '<legend>The Directors Directing Any Videos in Library '.strip_tags($lib['library_name']).'</legend>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
		echo 'Search director by name & surname:&nbsp;<input type="text" name="director_name" />&nbsp;';
		echo '<input type="submit" value="Search" />';
		echo '</form><br />';
		if ( isset( $_POST['director_name']) ) {
			$directorsFetch = mysql_query( 'SELECT DISTINCT director_id, director_first_name, director_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Director WHERE director_first_name LIKE "%'.mysql_real_escape_string( $_POST['director_name']).'%" OR director_last_name LIKE "%'.mysql_real_escape_string( $_POST['director_name']).'%" AND library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $directorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Director Name</th><th>Actions</th></tr>';
				while ( $director = mysql_fetch_array( $directorsFetch) ) {
					echo '<tr><td>'.strip_tags( $director['director_first_name'].' '.$director['director_last_name']).'</td><td><a href="action.php?todo=removedirector&aid='.strip_tags($director['director_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no director in the system with the given names.</p>';
			}
		}
		else {
			$directorsFetch = mysql_query( 'SELECT director_id, director_first_name, director_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Director WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $directorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Director Name</th><th>Actions</th></tr>';
				while ( $director = mysql_fetch_array( $directorsFetch) ) {
					echo '<tr><td>'.strip_tags( $director['director_first_name'].' '.$director['director_last_name']).'</td><td><a href="action.php?todo=removedirector&lid='.$_GET['lid'].'&aid='.strip_tags($director['director_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no director in the system with the given names.</p>';
			}
		}
		echo '</fieldset>';
		echo '</body></html>';
	}
	else {
		header( 'Location: index.php');
	}
}
?>
