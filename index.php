<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( !isset( $_SESSION['visitor_id']) ) {
	if ( !isset( $_POST['userid']) && !isset( $_POST['userpwd']) ) {
		display_page_head( 'LibMan | Login');
		echo '<body>';
		echo "<fieldset>";
		echo "<legend>Log In</legend>";
		echo '<h2>LibMan Library DBMS</h2>';
		echo '<img src="res/img/News39645.png" width="180" height="200" />';
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		echo '<table style="border:0;">';
		echo '<tr><td>User ID</td><td>:</td><td><input type="text" name="userid" /></td></tr>';
		echo '<tr><td>Password</td><td>:</td><td><input type="password" name="userpwd" /></td></tr>';
		echo '<tr><td colspan="2"><input type="submit" value="Log In" /></td></tr>';
		echo '</table>';
		echo '</form>';
		echo "</fieldset>";
		if ( isset( $_GET['st']) ) {
			switch ( $_GET['st']) {
				case '1':
					echo '<p class="warning">The visitor with the given id and password does NOT exist in the system!</p>';
				break;
			}
		}
		echo '</body></html>';
	}
	else {
		$conn = connectToSql();
		$userid = mysql_real_escape_string( $_POST['userid']);
		$userpwd = mysql_real_escape_string( $_POST['userpwd']);
		$users = mysql_query( 'SELECT * FROM Visitor WHERE visitor_id = '.$userid.' AND visitor_pwd = "'.$userpwd.'";', $conn);
		
		if ( mysql_num_rows( $users) == 1 ) {
			$user = mysql_fetch_array( $users);
			$_SESSION['visitor_id'] = $user['visitor_id'];
			$_SESSION['visitor_first_name'] = $user['visitor_first_name'];
			header( 'Location: '.$_SERVER['PHP_SELF']);
		}
		else {
			header( 'Location: '.$_SERVER['PHP_SELF'].'?st=1');
		}
	}
}
else {
	/*
	$conn = connectToSql();
	$userInfoFetch = mysql_query( 'SELECT * FROM Visitor,Library WHERE visitor_id = '.$_SESSION['visitor_id'].' AND visitor_room_libid = library_id;', $conn) or die( mysql_error() );
	$userInfo = mysql_fetch_array( $userInfoFetch);
	display_page_head( 'LibMan | '.$userInfo['visitor_id']);
	echo '<body>';
	//get_status_bar( $userInfo['visitor_id'], $userInfo['visitor_first_name']);
	echo '<h2>Hello, '.$userInfo['visitor_first_name'].'</h2>';
	echo '<table>';
	echo '<tr><td>Full Name</td><td>:</td><td>'.$userInfo['visitor_first_name'].' '.$userInfo['visitor_last_name'].'</td></tr>';
	echo '</table>';
	if ( $userInfo['visitor_room_no'] != 'NULL') {
		$startTime = strtotime( $userInfo['visitor_room_start_time']);
		$endTime = strtotime( $userInfo['visitor_room_end_time']);
		if ( $startTime <= time() && time() <= $endTime) {
			echo 'You are marked in '.$userInfo['library_name'].' room '.$userInfo['visitor_room_no'].'!<br />';
			echo 'Your room session will end on '.date( 'd/m/Y H:i', $endTime).'<br />';
		}
	}
	$userLibraries = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library WHERE visitor_id = '.$_SESSION['visitor_id'].' ORDER BY auth_level ASC;', $conn);
	echo '<fieldset>';
	if ( mysql_num_rows( $userLibraries) ) {
		echo '<legend>The libraries you are in</legend>';
		echo '<table>';
		echo '<tr><th>Library Name</th><th>Authentication Level</th></tr>';
		while ( $userLibInfo = mysql_fetch_array( $userLibraries) ) {
			echo '<tr><td><a href="libmanager.php?lid='.$userLibInfo['library_id'].'">'.$userLibInfo['library_name'].'</a></td>';
			switch ( $userLibInfo['auth_level']) {
				case 'admin':
					echo '<td>Administrative Staff</td>';
				break;
				
				case 'only':
					echo '<td>Visitor</td>';
				break;
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	else {
		echo 'There is no library you are in.';
	}
	echo '</fieldset></body></html>';*/
	header( 'Location: visitors.php');
}
?>
