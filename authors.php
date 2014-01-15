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
		display_page_head( 'LibMan | Manage Authors in Library '.strip_tags($lib['library_name']));
		echo '<body>';
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="success">The author and its books have successfully been removed from the system!</p>';
				break;
				
				case '2':
					echo '<p class="warning">Error removing the author, contact the administrator!</p>';
				break;
			}
		}
		echo '<fieldset>';
		echo '<legend>The Authors Playing in Any Videos in Library '.strip_tags($lib['library_name']).'</legend>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
		echo 'Search author by name & surname:&nbsp;<input type="text" name="author_name" />&nbsp;';
		echo '<input type="submit" value="Search" />';
		echo '</form><br />';
		if ( isset( $_POST['author_name']) ) {
			$authorsFetch = mysql_query( 'SELECT DISTINCT author_id, author_first_name, author_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book NATURAL JOIN Is_Written_By NATURAL JOIN Author WHERE author_first_name LIKE "%'.mysql_real_escape_string( $_POST['author_name']).'%" OR author_last_name LIKE "%'.mysql_real_escape_string( $_POST['author_name']).'%" AND library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $authorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Author Name</th><th>Actions</th></tr>';
				while ( $author = mysql_fetch_array( $authorsFetch) ) {
					echo '<tr><td>'.strip_tags( $author['author_first_name'].' '.$author['author_last_name']).'</td><td><a href="action.php?todo=removeauthor&aid='.strip_tags($author['author_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no author in the system with the given names.</p>';
			}
		}
		else {
			$authorsFetch = mysql_query( 'SELECT DISTINCT author_id, author_first_name, author_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book NATURAL JOIN Is_Written_By NATURAL JOIN Author WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $authorsFetch) > 0 ) {
				echo '<table>';
				echo '<tr><th>Author Name</th><th>Actions</th></tr>';
				while ( $author = mysql_fetch_array( $authorsFetch) ) {
					echo '<tr><td>'.strip_tags( $author['author_first_name'].' '.$author['author_last_name']).'</td><td><a href="action.php?todo=removeauthor&lid='.$_GET['lid'].'&aid='.strip_tags($author['author_id']).'">Remove</a></td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '<p class="info">There is no author in the system with the given names.</p>';
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
