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
		display_page_head( 'LibMan | Manage Actors in Library '.strip_tags($lib['library_name']));
		echo '<body>';
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="success">The actor and its videos have successfully been removed from the system!</p>';
				break;
				
				case '2':
					echo '<p class="warning">Error removing the actor, contact the administrator!</p>';
				break;
			}
		}
		echo '<fieldset>';
		echo '<legend>The Actors Playing in Any Videos in Library '.strip_tags($lib['library_name']).'</legend>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
		echo 'Search actor by name & surname:&nbsp;<input type="text" name="actor_name" />&nbsp;';
		echo '<input type="submit" value="Search" />';
		echo '</form><br />';
		if ( isset( $_POST['actor_name']) ) {
			$actorsFetch = mysql_query( 'SELECT DISTINCT actor_id, actor_first_name, actor_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Actor WHERE actor_first_name LIKE "%'.mysql_real_escape_string( $_POST['actor_name']).'%" OR actor_last_name LIKE "%'.mysql_real_escape_string( $_POST['actor_name']).'%" AND library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $actorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Actor Name</th><th>Actions</th></tr>';
				while ( $actor = mysql_fetch_array( $actorsFetch) ) {
					echo '<tr><td>'.strip_tags( $actor['actor_first_name'].' '.$actor['actor_last_name']).'</td><td><a href="action.php?todo=removeactor&lid='.$_GET['lid'].'&aid='.strip_tags($actor['actor_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no actor in the system with the given names.</p>';
			}
		}
		else {
			$actorsFetch = mysql_query( 'SELECT DISTINCT actor_id, actor_first_name, actor_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Actor WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $actorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Actor Name</th><th>Actions</th></tr>';
				while ( $actor = mysql_fetch_array( $actorsFetch) ) {
					echo '<tr><td>'.strip_tags( $actor['actor_first_name'].' '.$actor['actor_last_name']).'</td><td><a href="action.php?todo=removeactor&aid='.strip_tags($actor['actor_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no actor in the system with the given names.</p>';
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
