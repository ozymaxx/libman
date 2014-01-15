<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( isset( $_SESSION['visitor_id']) ) {
	if ( isset( $_GET['lid']) ) {
		if ( isset( $_GET['todo']) ) {
			$conn = connectToSql();
			switch ( $_GET['todo']) {
				case 'usepolicy':
					if ( isset( $_GET['toaddname']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Managed_By NATURAL JOIN Library_Hours_Policy WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND policy_name="'.mysql_real_escape_string( $_GET['toaddname']).'";', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $check) == 0 ) {
							if ( mysql_query( 'INSERT INTO Managed_By(policy_name, library_id) VALUES("'.mysql_real_escape_string( $_GET['toaddname']).'", '.mysql_real_escape_string( $_GET['lid']).');', $conn) ) {
								header( 'Location: policies.php?lid='.$_GET['lid'].'&st=5');
							}
							else {
								die( mysql_error() );
								header( 'Location: policies.php?lid='.$_GET['lid'].'&st=4');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'discardpolicy':
					if ( isset( $_GET['toaddname']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Managed_By NATURAL JOIN Library_Hours_Policy WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND policy_name="'.mysql_real_escape_string( $_GET['toaddname']).'";', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $check) > 0 ) {
							if ( mysql_query( 'DELETE FROM Managed_By WHERE library_id='.mysql_real_escape_string( $_GET['lid']).' AND  policy_name = "'.mysql_real_escape_string( $_GET['toaddname']).'";', $conn) ) {
								header( 'Location: policies.php?lid='.$_GET['lid'].'&st=6');
							}
							else {
								header( 'Location: policies.php?lid='.$_GET['lid'].'&st=7');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
			
				case 'removeactor':
					if ( isset( $_GET['aid']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Actor WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND actor_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows($check) > 0 ) {
							if ( mysql_query( 'DELETE FROM Actor WHERE actor_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) ) {
								header( 'Location: actors.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								header( 'Location: actors.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'removedirector':
					if ( isset( $_GET['aid']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Director WHERE auth_level="admin" library_id = '.mysql_real_escape_string( $_GET['lid']).' AND director_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows($check) > 0 ) {
							if ( mysql_query( 'DELETE FROM Director WHERE director_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) ) {
								header( 'Location: directors.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								header( 'Location: directors.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'removeauthor':
					if ( isset( $_GET['aid']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book NATURAL JOIN Is_Written_By Author WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND author_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows($check) > 0 ) {
							if ( mysql_query( 'DELETE FROM Author WHERE author_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) ) {
								header( 'Location: authors.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								header( 'Location: authors.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'removerecorder':
					if ( isset( $_GET['aid']) ) {
						$check = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND recorder_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) or die( mysql_error() );
						$check2 = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Recorder WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND recorder_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows($check) > 0 || mysql_num_rows( $check2) > 0) {
							if ( mysql_query( 'DELETE FROM Recorder WHERE recorder_id = '.mysql_real_escape_string( $_GET['aid']).';', $conn) ) {
								header( 'Location: recorder.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								die( mysql_error() );
								header( 'Location: recorder.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'borrowback':
					if ( isset( $_GET['itemid']) ) {
						$test = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE auth_level = "admin" AND library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $_GET['itemid']).' AND borrowed_visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $test) > 0 ) {
							if ( mysql_query( 'UPDATE Item SET borrowed_visitor_id = NULL, borrow_start_time = NULL, borrow_end_time = NULL WHERE item_id = '.mysql_real_escape_string( $_GET['itemid']).';', $conn) ) {
								header( 'Location: borrows.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								header( 'Location: borrows.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'reserveback':
					if ( isset( $_GET['itemid']) ) {
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $_GET['itemid']).' AND reserved_visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $test) > 0 ) {
							if ( mysql_query( 'UPDATE Item SET reserved_visitor_id = NULL, reserve_start_time = NULL, reserve_end_supposed_time = NULL WHERE item_id = '.mysql_real_escape_string( $_GET['itemid']).';', $conn) ) {
								header( 'Location: reservations.php?lid='.$_GET['lid'].'&st=1');
							}
							else {
								header( 'Location: reservations.php?lid='.$_GET['lid'].'&st=2');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'borrow':
					if ( isset( $_GET['itemid']) ) {
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $_GET['itemid']).' AND borrowed_visitor_id IS NULL AND reserved_visitor_id IS NULL;', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $test) > 0 ) {
							if ( mysql_query( 'UPDATE Item SET borrow_count = borrow_count + 1, borrow_start_time = NOW(), borrow_end_time = TIMESTAMPADD( WEEK, 2, NOW() ), borrowed_visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).' WHERE item_id = '.mysql_real_escape_string( $_GET['itemid']).';', $conn) ) {
								header( 'Location: borrows.php?lid='.$_GET['lid'].'&st=3');
							}
							else {
								header( 'Location: borrows.php?lid='.$_GET['lid'].'&st=4');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
				
				case 'reserve':
					if ( isset( $_GET['itemid']) ) {
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $_GET['itemid']).' AND borrowed_visitor_id IS NULL AND reserved_visitor_id IS NULL;', $conn) or die( mysql_error() );
						if ( mysql_num_rows( $test) > 0 ) {
							if ( mysql_query( 'UPDATE Item SET reserve_count = reserve_count + 1, reserve_start_time = NOW(), reserve_end_supposed_time = TIMESTAMPADD( WEEK, 1, NOW() ), reserved_visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).' WHERE item_id = '.mysql_real_escape_string( $_GET['itemid']).';', $conn) ) {
								header( 'Location: reservations.php?lid='.$_GET['lid'].'&st=3');
							}
							else {
								header( 'Location: reservations.php?lid='.$_GET['lid'].'&st=4');
							}
						}
						else {
							header( 'Location: index.php');
						}
					}
					else {
						header( 'Location: index.php');
					}
				break;
			}
		}
		else {
			header( 'Location: index.php');
		}
	}
	else {
		header( 'Location: index.php');
	}
}
else {
	header( 'Location: index.php');
}
?>
