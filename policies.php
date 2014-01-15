<?php
include_once( './lib/sql_connection.php');
include_once( './lib/funcs.php');

session_start();

if ( !isset( $_SESSION['visitor_id']) && !isset( $_GET['lid']) ) {
	header( 'Location: index.php');
}
else {
	if ( isset( $_POST['policy_name']) && isset( $_POST['time_start_min']) && isset( $_POST['time_start_hour'])
		&& isset( $_POST['time_end_min']) && isset( $_POST['time_end_hour']) && isset( $_POST['library_pol1_val_day'])
		&& isset( $_POST['library_pol1_val_year']) && isset( $_POST['library_pol1_val_mon'])
		&& isset( $_POST['library_pol1_val_hour']) && isset( $_POST['library_pol1_val_min']) && isset( $_POST['library_pol2_val_day'])
		&& isset( $_POST['library_pol2_val_year']) && isset( $_POST['library_pol2_val_mon'])
		&& isset( $_POST['library_pol2_val_hour']) && isset( $_POST['library_pol2_val_min']) ) {
			$conn = connectToSql();
			$libCheck = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library WHERE library_id='.mysql_real_escape_string( $_GET['lid']).' AND auth_level="admin" AND visitor_id='.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $libCheck) == 1 ) {
				$policyCheck = mysql_query( 'SELECT * FROM Library_Hours_Policy WHERE policy_name = "'.mysql_real_escape_string( $_POST['policy_name']).'";', $conn);
				if ( mysql_num_rows( $policyCheck) == 1 ) {
					header( 'Location: '.$_SERVER['PHP_SELF'].'?st=1');
				}
				else {
					if ( checkdate( intval( $_POST['library_pol1_val_mon']), intval( $_POST['library_pol1_val_day']), intval( $_POST['library_pol1_val_year']) ) &&
						checkdate( intval( $_POST['library_pol2_val_mon']), intval( $_POST['library_pol2_val_day']), intval( $_POST['library_pol2_val_year']) ) ) {
						$date1 = $_POST['library_pol1_val_year'].'-'.$_POST['library_pol1_val_mon'].'-'.$_POST['library_pol1_val_day'].' '.$_POST['library_pol1_val_hour'].':'.$_POST['library_pol1_val_min'].':00';
						$date2 = $_POST['library_pol2_val_year'].'-'.$_POST['library_pol2_val_mon'].'-'.$_POST['library_pol2_val_day'].' '.$_POST['library_pol2_val_hour'].':'.$_POST['library_pol2_val_min'].':00';
						$time1 = $_POST['time_start_hour'].':'.$_POST['time_start_min'].':00';
						$time2 = $_POST['time_end_hour'].':'.$_POST['time_end_min'].':00';
						mysql_query( 'INSERT INTO Library_Hours_Policy(policy_name, opening_time, closing_time, policy_validity_start, policy_validity_end) VALUES("'.mysql_real_escape_string( strip_tags( $_POST['policy_name']) ).'","'.mysql_real_escape_string( strip_tags( $time1) ).'","'.mysql_real_escape_string( strip_tags( $time2) ).'","'.mysql_real_escape_string( strip_tags( $date1) ).'","'.mysql_real_escape_string( strip_tags( $date2) ).'");', $conn) or die( mysql_error() );
						header( 'Location: '.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=2');
					}
					else {
						header( 'Location: '.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'&st=3');
					}
				}
			}
			else {
				header( 'Location: index.php');
			}
	}
	else {
		if ( isset( $_GET['lid']) ) {
			$conn = connectToSql();
			$libFetch = mysql_query( 'SELECT * FROM Visits NATURAL JOIN Library WHERE library_id = '.mysql_real_escape_string( $_GET['lid']).' AND auth_level = "admin" AND visitor_id='.mysql_real_escape_string( $_SESSION['visitor_id']).';', $conn) or die( mysql_error() );
			if ( mysql_num_rows( $libFetch) == 1 ) {
				$lib = mysql_fetch_array( $libFetch); 
				display_page_head( 'LibMan | Policies Management of '.$lib['library_name']);
				echo '<body>';
				echo '<a href="logout.php">Logout</a><br />';
				echo '<a href="libmanager.php?lid='.$_GET['lid'].'">Back To Main Library Screen Menu</a><br />';
				echo '<fieldset>';
				if ( isset( $_GET['st']) ) {
					switch ( $_GET['st']) {
						case '1':
							echo '<p class="warning">There is a policy with the given name, try a different name!</p>';
						break;
						
						case '2':
							echo '<p class="success">The policy has been successfully added!</p>';
						break;
						
						case '3':
							echo '<p class="warning">The given dates for the  range is not valid!</p>';
						break;
						
						case '4':
							echo '<p class="warning">The policy you have chosen overlaps a policy used for this library. Please use another time policy.</p>';
						break;
						
						case '5':
							echo '<p class="success">The policy has been successfully added to the library policies.</p>';
						break;
						
						case '6':
							echo '<p class="success">The policy has been successfully removed from the library policies.</p>';
						break;
						
						case '7':
							echo '<p class="warning">Database error, contact the administrator!</p>';
						break;
					}
				}
				echo '<legend>Add Policy</legend>';
				echo '<form action="'.$_SERVER['PHP_SELF'].'?lid='.$_GET['lid'].'" method="POST">';
				echo '<table>';
				echo '<tr><td>Policy Name</td><td>:</td><td><input type="text" name="policy_name" maxlength="100" /></td></tr>';
				echo '<tr><td>Library will open at</td><td>:</td><td><select name="time_start_hour">';
				for ( $i = 0; $i <= 23; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>:<select name="time_start_min">';
				for ( $i = 0; $i <= 59; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select></td></tr>';
				echo '<tr><td>Library will close at</td><td>:</td><td><select name="time_end_hour">';
				for ( $i = 0; $i <= 23; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>:<select name="time_end_min">';
				for ( $i = 0; $i <= 59; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select></td></tr>';
				echo '<tr><td>This policy will be valid from</td><td>:</td><td><select name="library_pol1_val_day">';
				for ( $i = 1; $i <= 31; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>/<select name="library_pol1_val_mon">';
				echo '<option value="01">January</option>';
				echo '<option value="02">February</option>';
				echo '<option value="03">March</option>';
				echo '<option value="04">April</option>';
				echo '<option value="05">May</option>';
				echo '<option value="06">June</option>';
				echo '<option value="07">July</option>';
				echo '<option value="08">August</option>';
				echo '<option value="09">September</option>';
				echo '<option value="10">October</option>';
				echo '<option value="11">November</option>';
				echo '<option value="12">December</option>';
				echo '</select>&nbsp;<select name="library_pol1_val_year">';
				for ( $i = 1970; $i <= date( 'Y') + 3; $i++) {
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select><select name="library_pol1_val_hour">';
				for ( $i = 0; $i <= 23; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>:<select name="library_pol1_val_min">';
				for ( $i = 0; $i <= 59; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>';
				echo '</td></tr>';
				echo '<tr><td>This policy will be valid until</td><td>:</td><td><select name="library_pol2_val_day">';
				for ( $i = 1; $i <= 31; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>/<select name="library_pol2_val_mon">';
				echo '<option value="01">January</option>';
				echo '<option value="02">February</option>';
				echo '<option value="03">March</option>';
				echo '<option value="04">April</option>';
				echo '<option value="05">May</option>';
				echo '<option value="06">June</option>';
				echo '<option value="07">July</option>';
				echo '<option value="08">August</option>';
				echo '<option value="09">September</option>';
				echo '<option value="10">October</option>';
				echo '<option value="11">November</option>';
				echo '<option value="12">December</option>';
				echo '</select>&nbsp;<select name="library_pol2_val_year">';
				for ( $i = 1970; $i <= date( 'Y') + 3; $i++) {
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select><select name="library_pol2_val_hour">';
				for ( $i = 0; $i <= 23; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>:<select name="library_pol2_val_min">';
				for ( $i = 0; $i <= 59; $i++) {
					if ( $i < 10) {
						echo '<option value="0'.$i.'">0'.$i.'</option>';
					}
					else {
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				echo '</select>';
				echo '</td></tr>';
				echo '<tr><td colspan="3"><input type="submit" value="Add Policy" /></td></tr>';
				echo '</table>';
				echo '</form>';
				echo '</fieldset>';
				echo '<fieldset>';
				echo '<legend>The Whole List of Policies</legend>';
				$polFetch = mysql_query( 'SELECT policy_name, opening_time, closing_time, policy_validity_start, policy_validity_end FROM Library_Hours_Policy GROUP BY policy_name;', $conn) or die( mysql_error() );
				if ( mysql_num_rows( $polFetch) > 0 ) {
					echo '<table>';
					echo '<tr><th>Policy Name</th><th>Opening Time</th><th>Closing Time</th><th>Valid From</th><th>Valid To</th><th>Actions</th></tr>';
					while ( $policy = mysql_fetch_array( $polFetch) ) {
						echo '<tr><td>'.strip_tags($policy['policy_name']).'</td><td>'.strip_tags($policy['opening_time']).'</td><td>'.strip_tags($policy['closing_time']).'</td><td>'.date( 'l jS \of F Y h:i:s A', strtotime( strip_tags($policy['policy_validity_start'])) ).'</td><td>'.date( 'l jS \of F Y h:i:s A', strtotime(strip_tags($policy['policy_validity_end']))).'</td><td><a href="action.php?todo=usepolicy&toaddname='.strip_tags($policy['policy_name']).'&lid='.$_GET['lid'].'">Use</a></td></tr>';
					}
					echo '</table>';
				}
				else {
					echo '<p class="info">There is no library hour policy in the system.</p>';
				}
				echo '</fieldset>';
				echo '<fieldset>';
				echo '<legend>The Library Hours Policies Used By Your Library</legend>';
				$polFetch = mysql_query( 'SELECT policy_name, opening_time, closing_time, policy_validity_start, policy_validity_end FROM Managed_By NATURAL JOIN Library_Hours_Policy WHERE library_id='.mysql_real_escape_string( $_GET['lid']).';', $conn) or die( mysql_error() );
				if ( mysql_num_rows( $polFetch) > 0 ) {
					echo '<table>';
					echo '<tr><th>Policy Name</th><th>Opening Time</th><th>Closing Time</th><th>Valid From</th><th>Valid To</th><th>Actions</th></tr>';
					while ( $policy = mysql_fetch_array( $polFetch) ) {
						echo '<tr><td>'.strip_tags($policy['policy_name']).'</td><td>'.strip_tags($policy['opening_time']).'</td><td>'.strip_tags($policy['closing_time']).'</td><td>'.date( 'l jS \of F Y h:i:s A', strtotime( strip_tags($policy['policy_validity_start'])) ).'</td><td>'.date( 'l jS \of F Y h:i:s A', strtotime(strip_tags($policy['policy_validity_end']))).'</td><td><a href="action.php?todo=discardpolicy&toaddname='.strip_tags($policy['policy_name']).'&lid='.$_GET['lid'].'">Discard</a></td></tr>';
					}
					echo '</table>';
				}
				else {
					echo '<p class="info">There is no library hour policy in the system.</p>';
				}
				echo '</fieldset>';
				echo '</body>';
			}
		}
	}
}
?>
