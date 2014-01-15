<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( isset( $_SESSION['visitor_id']) ) {
	if ( isset( $_GET['lid']) ) {
		$conn = connectToSql();
		$libCheck = mysql_query( 'SELECT * FROM Library NATURAL JOIN Visits WHERE visitor_id = '.mysql_real_escape_string( $_SESSION['visitor_id']).' AND library_id='.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
		if ( mysql_num_rows( $libCheck) == 1 ) {
			if ( !isset( $_GET['dispid']) ) {
				$lib = mysql_fetch_array( $libCheck);
				display_page_head( 'LibMan | Browse '.$lib['library_name']);
				echo '<body>';
				echo '<a href="logout.php">Logout</a><br />';
				echo '<a href="visitors.php">Back To Visitors Menu</a><br />';
				if ( isset( $_POST['item_name']) ) {
					if ( $_POST['s_audio'] != 'Y' && $_POST['s_video'] != 'Y' && $_POST['s_book'] != 'Y') {
						//die( "problem1");
						echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=1" />');
					}
					else {
						echo '<a href="display.php?lid='.$_GET['lid'].'">Back To Library Browser</a><br />';
						if ( $_POST['s_book'] == 'Y') {
							if ( strlen( $_POST['item_name']) > 0 ) {
								$query = 'SELECT DISTINCT item_id, item_name, borrowed_visitor_id, reserved_visitor_id, publication_date, borrow_count, reserve_count, section_name, shelf_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book NATURAL JOIN Is_Written_By NATURAL JOIN Author WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND shelf_type = "book" ';
								$query .= 'AND item_name LIKE "%'.mysql_real_escape_string( $_POST['item_name']).'%" ';
								if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) > 0) {
									if ( is_numeric( $_POST['pub_date_start']) && is_numeric( $_POST['pub_date_end']) ) {
										if ( intval( $_POST['pub_date_end']) >= intval( $_POST['pub_date_start']) ) {
											$query .= 'AND publication_date BETWEEN '.mysql_real_escape_string( $_POST['pub_date_start']).' AND '.mysql_real_escape_string( $_POST['pub_date_end']).' ';
											if ( strlen( $_POST['page_count_start']) > 0 && strlen( $_POST['page_count_end']) > 0 ) {
												if ( intval( $_POST['page_count_start']) >= 0 && intval( $_POST['page_count_end']) >= intval( $_POST['page_count_start']) ) {
													$query .= 'AND page_count BETWEEN '.mysql_real_escape_string( $_POST['page_count_start']).' AND '.mysql_real_escape_string( $_POST['page_count_end']).' ';
												}
												else {
													//die ("problem14");
													echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
												}
											}
											
											if ( strlen( $_POST['author']) > 0) {
												$query .= 'AND author_name LIKE "%'.mysql_real_escape_string( $_POST['author_name']).'%" ';
											}
										}
										else {
											//die ("problem2");
											echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
										}
									}
									else {
										//die ("problem3");
										echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
									}
								}
								else if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) == 0 || strlen( $_POST['pub_date_start']) == 0 && strlen( $_POST['pub_date_end']) > 0) {
									//die ("problem4");
									echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
								}
								
								if ( $_POST['borrowed'] == 'Y') {
									$query .= 'AND borrowed_visitor_id = NULL ';
								}
								
								if ( $_POST['reserved'] == 'Y') {
									$query .= 'AND reserved_visitor_id = NULL ';
								}
								$query .= 'ORDER BY publication_date DESC;';
								$items = mysql_query( $query, $conn) or die( $query );
								echo '<fieldset>';
								echo '<legend>Books in '.$lib['library_name'].'</legend>';
								if ( mysql_num_rows( $items) > 0 ) {
									echo '<table>';
									echo '<tr><td>Book Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Actions</td></tr>';
									while ( $item = mysql_fetch_array( $items) ) {
										echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td>';
										if ( $item['borrowed_visitor_id'] === null && $item['reserved_visitor_id'] === null) {
											echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
										}
										else {
											echo '<td>Borrowed or reserved!</td>';
										}
										echo '</tr>';
									} 
									echo '</table>';
								}
								else {
									echo '<p class="info">There is no book in the system.</p>';
								}
								echo '</fieldset><br />';
							}
							else {
								//die ("problem5");
								echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
							}
						}
						
						if ( $_POST['s_video'] == 'Y') {
							if ( strlen( $_POST['item_name']) > 0 ) {
								$query = 'SELECT DISTINCT item_id, item_name, publication_date, borrowed_visitor_id, reserved_visitor_id, borrow_count, reserve_count, section_name, shelf_name, duration FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Director NATURAL JOIN Actor WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND shelf_type = "video" ';
								$query .= 'AND item_name LIKE "%'.mysql_real_escape_string( $_POST['item_name']).'%" ';
								if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) > 0) {
									if ( is_numeric( $_POST['pub_date_start']) && is_numeric( $_POST['pub_date_end']) ) {
										if ( intval( $_POST['pub_date_end']) >= intval( $_POST['pub_date_start']) ) {
											$query .= 'AND publication_date BETWEEN '.mysql_real_escape_string( $_POST['pub_date_start']).' AND '.mysql_real_escape_string( $_POST['pub_date_end']).' ';
										}
										else {
											//die ("problem6");
											echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
										}
										
										if ( strlen( $_POST['vduration_start1']) > 0 && strlen( $_POST['vduration_start2']) > 0 &&
											strlen( $_POST['vduration_end1']) > 0 && strlen( $_POST['vduration_end2']) > 0) {
											$durationStart = $_POST['vduration_start1'].':'.$_POST['vduration_start2'].':00';
											$durationEnd = $_POST['vduration_end1'].':'.$_POST['vduration_end2'].':00';
											$query .= 'AND duration BETWEEN "'.mysql_real_escape_string( $durationStart).'" AND "'.mysql_real_escape_string( $durationEnd).'" ';
										}
										
										if ( strlen( $_POST['director']) > 0) {
											$query .= 'AND director_name LIKE "%'.mysql_real_escape_string( $_POST['director']).'%" ';
										}
										
										if ( strlen( $_POST['actor']) > 0 ) {
											$actorList = preg_split('#(&|,|;|.| )#', mysql_real_escape_string($_POST['actor']));
											if ( count( $actorsList) > 0) {
												$query .= 'AND ';
												for ( $i = 0; $i < count( $actorsList); $i++ ) {
													if ( $i == count( $actorsList) - 1 ) {
														$query .= 'actor_name LIKE "%'.mysql_real_escape_string( $actorsList[$i]).'%" OR ';
													}
													else {
														$query .= 'actor_name LIKE "%'.mysql_real_escape_string( $actorsList[$i]).'%" ';
													}
												}
											}
										}
									}
									else {
										//die ("problem7");
										echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
									}
								}
								else if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) == 0 || strlen( $_POST['pub_date_start']) == 0 && strlen( $_POST['pub_date_end']) > 0) {
									//die ("problem8");
									echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2" />');
								}
								
								if ( $_POST['borrowed'] == 'Y') {
									$query .= 'AND borrowed_visitor_id = NULL ';
								}
								
								if ( $_POST['reserved'] == 'Y') {
									$query .= 'AND reserved_visitor_id = NULL';
								}
								$query .= 'ORDER BY publication_date DESC;';
								$items = mysql_query( $query, $conn) or die( mysql_error() );
								echo '<fieldset>';
								echo '<legend>Videos in '.$lib['library_name'].'</legend>';
								if ( mysql_num_rows( $items) > 0 ) {
									echo '<table>';
									echo '<tr><td>Audio Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Duration</td><td>Actions</td></tr>';
									while ( $item = mysql_fetch_array( $items) ) {
										echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td><td>'.strip_tags( date( 'G:i', strtotime($item['duration']))).'</td>';
										if ( $item['borrowed_visitor_id'] === null && $item['reserved_visitor_id'] === null) {
											echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
										}
										else {
											echo '<td>Borrowed or reserved!</td>';
										}
										echo '</tr>';
									} 
									echo '</table>';
								}
								else {
									echo '<p class="info">There is no video in the system.</p>';
								}
								echo '</fieldset><br />';
							}
							else {
								//die ("problem9");
								echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
							}
						}
						
						if ( $_POST['s_audio'] == 'Y') {
							if ( strlen( $_POST['item_name']) > 0 ) {
								$query = 'SELECT DISTINCT item_id, item_name, publication_date, borrow_count, borrowed_visitor_id, reserved_visitor_id, reserve_count, shelf_name, section_name, duration, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE library_id="'.mysql_real_escape_string( $_GET['lid']).'" AND shelf_type="audio" ';
								$query .= 'AND item_name LIKE "%'.mysql_real_escape_string( $_POST['item_name']).'%" ';
								if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) > 0) {
									if ( is_numeric( $_POST['pub_date_start']) && is_numeric( $_POST['pub_date_end']) ) {
										if ( intval( $_POST['pub_date_end']) >= intval( $_POST['pub_date_start']) ) {
											$query .= 'AND publication_date BETWEEN '.mysql_real_escape_string( $_POST['pub_date_start']).' AND '.mysql_real_escape_string( $_POST['pub_date_end']).' ';
										}
										else {
											//die ("problem10");
											echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
										}
										
										if ( strlen( $_POST['aduration_start1']) > 0 && strlen( $_POST['aduration_start2']) > 0 &&
											strlen( $_POST['aduration_end1']) > 0 && strlen( $_POST['aduration_end2']) > 0) {
											$durationStart = $_POST['aduration_start1'].':'.$_POST['aduration_start2'].':00';
											$durationEnd = $_POST['aduration_end1'].':'.$_POST['aduration_end2'].':00';
											$query .= 'AND duration BETWEEN "'.mysql_real_escape_string( $durationStart).'" AND "'.mysql_real_escape_string( $durationEnd).'" ';
										}
										
										if ( strlen( $_POST['recorder']) > 0) {
											$query .= 'AND recorder_first_name LIKE "%'.mysql_real_escape_string( $_POST['recorder']).'%" OR recorder_last_name LIKE "%'.mysql_real_escape_string( $_POST['recorder']).'%" ';
										}
									}
									else {
										//die ("problem11");
										echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
									}
								}
								else if ( strlen( $_POST['pub_date_start']) > 0 && strlen( $_POST['pub_date_end']) == 0 || strlen( $_POST['pub_date_start']) == 0 && strlen( $_POST['pub_date_end']) > 0) {
									//die ("problem12");
									echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
								}
								
								if ( $_POST['borrowed'] == 'Y') {
									$query .= 'AND borrowed_visitor_id = NULL ';
								}
								
								if ( $_POST['reserved'] == 'Y') {
									$query .= 'AND reserved_visitor_id = NULL';
								}
								$query .= 'ORDER BY publication_date DESC;';
								$items = mysql_query( $query, $conn) or die( mysql_error() );
								echo '<fieldset>';
								echo '<legend>Audios in '.$lib['library_name'].'</legend>';
								if ( mysql_num_rows( $items) > 0 ) {
									echo '<table>';
									echo '<tr><td>Audio Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Duration</td><td>Recorder</td><td>Actions</td></tr>';
									while ( $item = mysql_fetch_array( $items) ) {
										echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td><td>'.strip_tags( date( 'G:i', strtotime($item['duration']))).'</td><td>'.strip_tags( $item['recorder_first_name']).' '.strip_tags( $item['recorder_last_name']).'</td>';
										if ( $item['reserved_visitor_id'] === null && $item['borrowed_visitor_id'] === null) {
											echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
										}
										else {
											echo '<td>Borrowed or reserved!</td>';
										}
										echo '</tr>';
									} 
									echo '</table>';
								}
								else {
									echo '<p class="info">There is no audio in the system.</p>';
								}
								echo '</fieldset><br />';
							}
							else {
								//die ("problem13");
								echo( '<meta http-equiv="refresh" content="1; url='.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2;" />');
							}
						}
					}
				}
				else {
					if ( isset($_GET['st'])) {
						switch ( $_GET['st']) {
							case '1':
								echo '<p class="warning">You should be searching at least one of the item types!</p>';
							break;
							
							case '2':
								echo '<p class="warning">The inputs should be correct, please fix them!</p>';
							break;
						}
					}
					echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
					echo '<fieldset>';
					echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
					echo '<legend>Search</legend>';
					echo '<fieldset>';
					echo '<legend>Common Criteria</legend>';
					echo '<table>';
					echo '<tr><td>Name</td><td>:</td><td><input type="text" name="item_name" /></td></tr>';
					echo '<tr><td colspan="3">The publication date should be between the given years:</td></tr>';
					echo '<tr><td>Lower Bound</td><td>:</td><td><input type="text" name="pub_date_start" /></td></tr>';
					echo '<tr><td>Upper Bound</td><td>:</td><td><input type="text" name="pub_date_end" /></td></tr>';
					echo '<tr><td>Looking for NOT borrowed items?</td><td>:</td><td><input type="checkbox" name="borrowed" value="Y" /></td></tr>';
					echo '<tr><td>Looking for NOT reserved items?</td><td>:</td><td><input type="checkbox" name="reserved" value="Y" /></td></tr>';
					echo '</table>';
					echo '</fieldset><br />';
					echo '<fieldset>';
					echo '<legend>Book Criteria</legend>';
					echo 'Want to search books? <input type="checkbox" name="s_book" value="Y" /><br />';
					echo '<table>';
					echo '<tr><td>Publisher name</td><td>:</td><td><input type="text" name="publisher" /></td></tr>';
					echo '<tr><td>Author name</td><td>:</td><td><input type="text" name="author_name" /></td></tr>';
					echo '<tr><td colspan="3">The page count of books should be between in the given range:</td></tr>';
					echo '<tr><td>Lower Bound</td><td>:</td><td><input type="text" name="page_count_start" /></td></tr>';
					echo '<tr><td>Upper Bound</td><td>:</td><td><input type="text" name="page_count_end" /></td></tr>';
					echo '</table>';
					echo '</fieldset><br />';
					echo '<fieldset>';
					echo '<legend>Video Criteria</legend>';
					echo 'Want to search videos? <input type="checkbox" name="s_video" value="Y" /><br />';
					echo '<table>';
					echo '<tr><td>Director name</td><td>:</td><td><input type="text" name="director" /></td></tr>';
					echo '<tr><td>Actor name</td><td>:</td><td><input type="text" name="actor" /></td></tr>';
					echo '<tr><td colspan="3">The duration of videos should be in the given range:</td></tr>';
					echo '<tr><td>Lower Bound</td><td>:</td><td><input type="text" name="vduration_start1" />:<input type="text" name="vduration_start2" /></td></tr>';
					echo '<tr><td>Upper Bound</td><td>:</td><td><input type="text" name="vduration_end1" />:<input type="text" name="vduration_end2" /></td></tr>';
					echo '</table>';
					echo '</fieldset><br />';
					echo '<fieldset>';
					echo '<legend>Audio Criteria</legend>';
					echo 'Want to search audios? <input type="checkbox" name="s_audio" value="Y" /><br />';
					echo '<table>';
					echo '<tr><td>Recorder name</td><td>:</td><td><input type="" name="recorder" /></td></tr>';
					echo '<tr><td colspan="3">The duration of the videos should be in the given range</td></tr>';
					echo '<tr><td>Lower Bound</td><td>:</td><td><input type="text" name="aduration_start1" />:<input type="text" name="aduration_start2" /></td></tr>';
					echo '<tr><td>Upper Bound</td><td>:</td><td><input type="text" name="aduration_end1" />:<input type="text" name="aduration_end2" /></td></tr>';
					echo '</table>';
					echo '</fieldset>';
					echo '<input type="submit" value="Search" />';
					echo '</form>';
					echo '</fieldset>';
					$items = mysql_query( 'SELECT DISTINCT item_id, item_name, publication_date, borrow_count, reserve_count, section_name, borrowed_visitor_id, reserved_visitor_id, shelf_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND shelf_type = "book" ORDER BY publication_date DESC;', $conn) or die( mysql_error() );
					echo '<fieldset>';
					echo '<legend>Books in '.$lib['library_name'].'</legend>';
					if ( mysql_num_rows( $items) > 0 ) {
						echo '<table>';
						echo '<tr><td>Book Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Actions</td></tr>';
						while ( $item = mysql_fetch_array( $items) ) {
							echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td>';
							if ( $item['reserved_visitor_id'] === null && $item['borrowed_visitor_id'] === null) {
								echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
							}
							else {
								echo '<td>Borrowed or reserved!</td>';
							}
							echo '</tr>';
						} 
						echo '</table>';
					}
					else {
						echo '<p class="info">There is no book in the system.</p>';
					}
					echo '</fieldset><br />';
					$items = mysql_query( 'SELECT DISTINCT item_id, item_name, publication_date, borrowed_visitor_id, reserved_visitor_id, borrow_count, reserve_count, section_name, shelf_name, duration FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By NATURAL JOIN Director NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND shelf_type = "video" ORDER BY publication_date DESC;', $conn) or die( mysql_error() );
					echo '<fieldset>';
					echo '<legend>Videos in '.$lib['library_name'].'</legend>';
					if ( mysql_num_rows( $items) > 0 ) {
						echo '<table>';
						echo '<tr><td>Audio Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Duration</td><td>Actions</td></tr>';
						while ( $item = mysql_fetch_array( $items) ) {
							echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td><td>'.strip_tags( date( 'G:i', strtotime($item['duration']))).'</td>';
							if ( $item['reserved_visitor_id'] === null && $item['borrowed_visitor_id'] === null) {
								echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
							}
							else {
								echo '<td>Borrowed or reserved!</td>';
							}
							echo '</tr>';
						} 
						echo '</table>';
					}
					else {
						echo '<p class="info">There is no video in the system.</p>';
					}
					echo '</fieldset><br />';
					$items = mysql_query( 'SELECT DISTINCT item_id, item_name, borrowed_visitor_id, reserved_visitor_id, publication_date, borrow_count, reserve_count, shelf_name, section_name, duration, recorder_first_name, recorder_last_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND shelf_type = "audio" ORDER BY publication_date DESC;', $conn) or die( mysql_error() );
					echo '<fieldset>';
					echo '<legend>Audios in '.$lib['library_name'].'</legend>';
					if ( mysql_num_rows( $items) > 0 ) {
						echo '<table>';
						echo '<tr><td>Audio Name</td><td>Publication Year</td><td>Borrowed Times</td><td>Reserved Times</td><td>Path</td><td>Duration</td><td>Recorder</td><td>Actions</td></tr>';
						while ( $item = mysql_fetch_array( $items) ) {
							echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&dispid='.strip_tags( $item['item_id']).'">'.strip_tags( $item['item_name']).'</a></td><td>'.strip_tags( $item['publication_date']).'</td><td>'.strip_tags( $item['borrow_count']).'</td><td>'.strip_tags( $item['reserve_count']).'</td><td>SECTION('.strip_tags($item['section_name']).') &gt;&gt; SHELF('.strip_tags( $item['shelf_name']).')</td><td>'.strip_tags( date( 'G:i', strtotime($item['duration']))).'</td><td>'.strip_tags( $item['recorder_first_name']).' '.strip_tags( $item['recorder_last_name']).'</td>';
							if ( $item['reserved_visitor_id'] === null && $item['borrowed_visitor_id'] === null) {
								echo '<td><a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Borrow</a><a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.strip_tags( $item['item_id']).'">Reserve</a></td>';
							}
							else {
								echo '<td>Borrowed or reserved!</td>';
							}
							echo '</tr>';
						} 
						echo '</table>';
					}
					else {
						echo '<p class="info">There is no audio in the system.</p>';
					}
					echo '</fieldset><br />';
				}
				echo '</body>';
			}
			else {
			}
		}
		else {
			$itemCheck = mysql_query( 'SELECT item_name, picture_addr, borrow_count, reserve_count, publication_date, section_name, shelf_name, shelf_type, borrowed_visitor_id, reserved_visitor_id FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $itemCheck) >= 1 ) {
				$displayed = mysql_fetch_array( $itemCheck);
				echo '<body>';
				echo '<h2>'.strip_tags( $displayed['item_name']).'</h2>';
				echo '<img src="'.strip_tags( $displayed['picture_addr']).'" style="width:200px; height: 200px;" /><br />';
				echo '<i>Borrowed '.strip_tags( $displayed['borrow_count']).' times</i><br />';
				echo '<i>Reserved '.strip_tags( $displayed['reserve_count']).' times</i><br />';
				if ( $displayed['borrowed_visitor_id'] !== null) {
					echo '<i>This item is borrowed to someone.</i>';
				}
				else if ( $displayed['reserved_visitor_id'] !== null) {
					echo '<i>This item is under reservation</i>';
				}
				else {
					echo '<a href="action.php?todo=borrow&lid='.$_GET['lid'].'&itemid='.$displayed['item_id'].'">Borrow</a>';
					echo '<a href="action.php?todo=reserve&lid='.$_GET['lid'].'&itemid='.$displayed['item_id'].'">Reserve</a>';
				}
				echo '<br /><i>Published in '.strip_tags( $displayed['publication_date']).'</i><br />';
				echo '<i>It is in '.strip_tags( $displayed['section_name']).' section, '.strip_tags( $displayed['shelf_name']).'</i><br />';
				switch ( strip_tags($displayed['shelf_type'])) {
					case 'audio':
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Audio NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).';', $conn) or die( mysql_error() );
						$audio = mysql_fetch_array( $test);
						echo '<i>Recorded by '.strip_tags( $audio['recorder_first_name']).' '.strip_tags( $audio['recorder_last_name']).'</i><br />';
						echo '<i>Duration: '.strip_tags( $audio['duration']).'</i><br />';
					break;
					
					case 'video':
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Recorder WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).';', $conn) or die( mysql_error() );
						$video = mysql_fetch_array( $test);
						echo '<i>Duration: '.strip_tags( $video['duration']).'</i><br />';
						echo '<i>Recorded by '.strip_tags( $video['recorder_first_name']).' '.strip_tags( $video['recorder_last_name']).'</i><br />';
						$test = mysql_query( 'SELECT DISTINCT director_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By Director WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).' ORDER BY director_name ASC;', $conn) or die( mysql_error() );
						echo '<h3>Director(s):</h3>';
						echo '<ul>';
						while ( $director = mysql_fetch_array( $test) ) {
							echo '<li>'.strip_tags( $director['director_name']).'</li>';
						}
						echo '</ul><br />';
						$test = mysql_query( 'SELECT DISTINCT actor_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Video NATURAL JOIN Is_Produced_By Actor WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).' ORDER BY actor_name ASC;', $conn) or die( mysql_error() );
						echo '<h3>Actor(s):</h3>';
						echo '<ul>';
						while ( $act = mysql_fetch_array( $test) ) {
							echo '<li>'.strip_tags( $act['director_name']).'</li>';
						}
						echo '</ul><br />';
					break;
					
					case 'book':
						$test = mysql_query( 'SELECT * FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).';', $conn) or die( mysql_error() );
						$book = mysql_fetch_array( $test);
						echo '<i>The book has '.strip_tags( $book['page_count']).' pages</i><br />';
						echo '<i>Book is published by '.strip_tags( $book['publisher']).'</i><br />';
						$test = mysql_query( 'SELECT DISTINCT author_name FROM Library NATURAL JOIN Section NATURAL JOIN Shelf NATURAL JOIN Item NATURAL JOIN Book NATURAL JOIN Is_Written_By NATURAL JOIN Author WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND item_id = '.mysql_real_escape_string( $displayed['item_id']).' ORDER BY author_name ASC;', $conn) or die( mysql_error() );
						echo '<h3>Author(s):</h3>';
						echo '<ul>';
						while ( $author = mysql_fetch_array( $test) ) {
							echo '<li>'.strip_tags( $author['author_name']).'</li>';
						}
						echo '</ul>';
					break;
				}
				echo '</body>';
			}
			else {
				//die ("problem15");
				echo( '<body><meta http-equiv="refresh" content="1; url=index.php;" /></body>');
			}
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
