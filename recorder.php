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
		display_page_head( 'LibMan | Manage Recorder in Library '.strip_tags($lib['library_name']));
		echo '<body>';
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="success">The recorder and its videos / audios have successfully been removed from the system!</p>';
				break;
				
				case '2':
					echo '<p class="warning">Error removing the recorder, contact the administrator!</p>';
				break;
			}
		}
		echo '<fieldset>';
		echo '<legend>The Recorders for Any Videos in Library '.strip_tags($lib['library_name']).'</legend>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
		echo 'Search recorder by name & surname:&nbsp;<input type="text" name="recorder_name" />&nbsp;';
		echo '<input type="submit" value="Search" />';
		echo '</form><br />';
		if ( isset( $_POST['recorder_name']) ) {
			$recordersFetch = mysql_query( 'SELECT DISTINCT recorder_id, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Recorder WHERE recorder_first_name LIKE "%'.mysql_real_escape_string( $_POST['recorder_name']).'%" OR recorder_last_name LIKE "%'.mysql_real_escape_string( $_POST['recorder_name']).'%" AND library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			echo '<h3>Video Recorders</h3>';
			if ( mysql_num_rows( $recordersFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Recorder Name</th><th>Actions</th></tr>';
				while ( $recorder = mysql_fetch_array( $recordersFetch) ) {
					echo '<tr><td>'.strip_tags( $recorder['recorder_first_name'].' '.$recorder['recorder_last_name']).'</td><td><a href="action.php?todo=removerecorder&lid='.$_GET['lid'].'&aid='.strip_tags($recorder['recorder_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no director in the system with the given names.</p>';
			}
			
			$recordersFetch = mysql_query( 'SELECT DISTINCT recorder_id, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE recorder_first_name LIKE "%'.mysql_real_escape_string( $_POST['recorder_name']).'%" OR recorder_last_name LIKE "%'.mysql_real_escape_string( $_POST['recorder_name']).'%" AND library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			echo '<h3>Audio Recorders</h3>';
			if ( mysql_num_rows( $recordersFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Recorder Name</th><th>Actions</th></tr>';
				while ( $recorder = mysql_fetch_array( $recordersFetch) ) {
					echo '<tr><td>'.strip_tags( $recorder['recorder_first_name'].' '.$recorder['recorder_last_name']).'</td><td><a href="action.php?todo=removerecorder&lid='.$_GET['lid'].'&aid='.strip_tags($recorder['recorder_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no director in the system with the given names.</p>';
			}
		}
		else {
			$q = 'SELECT DISTINCT recorder_id, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).';';
			$recordersFetch = mysql_query( $q, $conn) or die( mysql_error() );
			echo '<h3>Video Recorders</h3>';
			if ( mysql_num_rows( $recordersFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Recorder Name</th><th>Actions</th></tr>';
				while ( $recorder = mysql_fetch_array( $recordersFetch) ) {
					echo '<tr><td>'.strip_tags( $recorder['recorder_first_name'].' '.$director['recorder_last_name']).'</td><td><a href="action.php?todo=removerecorder&lid='.$_GET['lid'].'&aid='.strip_tags($recorder['recorder_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no recorder in the system.</p>';
			}
			
			$q = 'SELECT DISTINCT recorder_id, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).';';
			$recordersFetch = mysql_query( $q, $conn) or die( mysql_error() );
			echo '<h3>Audio Recorders</h3>';
			if ( mysql_num_rows( $recordersFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Recorder Name</th><th>Actions</th></tr>';
				while ( $recorder = mysql_fetch_array( $recordersFetch) ) {
					echo '<tr><td>'.strip_tags( $recorder['recorder_first_name'].' '.$director['recorder_last_name']).'</td><td><a href="action.php?todo=removerecorder&lid='.$_GET['lid'].'&aid='.strip_tags($recorder['recorder_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no recorder in the system.</p>';
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
