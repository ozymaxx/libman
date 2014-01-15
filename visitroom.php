<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | Visit Any Room ');
	echo '<body>';

	$con = connectToSql();
	
	if( !isset( $_SESSION['visitor_id'] ) )
	{
		header( 'Location: index.php' ) ;
	}
	
	$visitor_id = $_SESSION['visitor_id'];
	
	if( !isset( $_GET['library_id'] ) )
	{
		header( 'Location: visitors.php' ) ;
	}
	
	$library_id = $_GET['library_id'];
	
	$query = "select * from Visitor natural join Visits where library_id='". $library_id. "' and visitor_id='". $visitor_id. "'";
	$result = mysql_query( $query, $con ) or die( mysql_error() );
	
	$visitor_room_no = null;
	$visitor_libid = null;
	$visiting = false;
	while( $row = mysql_fetch_array( $result ) )
	{
		/*if( $row['auth_level'] == 'admin' )
		{
			header( 'Location: visitors.php' ) ;
		}*/
		
		if( $row['visitor_room_no'] != null && $row['visitor_room_libid'] != null )
		{
			$visiting = true;
			$visitor_libid = $row['visitor_room_libid'];
			$visitor_room_no = $row['visitor_room_no'];
			break;
		}
	}
	
	echo '<a href="logout.php">Logout</a><br />';
	echo '<a href="visitors.php">Back To Main Library Screen Menu</a><br />';
	if( $visiting == true )
	{
		echo "You are visiting the room with no ". $visitor_room_no. " in the library with id ". $visitor_libid. " you should dump this room 
			to select another room";
		echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id." method='post' >";
			echo "<input type='hidden' name='func' value='dump_room' />";
			echo "<input type='hidden' name='visitor_room_no' value='". $visitor_room_no ."' />";
			echo "<input type='hidden' name='visitor_libid' value='". $visitor_libid ."' />";
			echo "<input type='submit' value='dump'>";
		echo "</form>";
	}
	else
	{
		$query = "select * from Library natural join Room where capacity > visit_count";
		$result = mysql_query( $query, $con ) or die( mysql_error() );
		
		echo "<fieldset>";
		echo "<legend>Visit a Room</legend>";
		echo "<table>";
			echo "<tr>";
				echo "<td>";
					echo "room no";
				echo "</td>";
				echo "<td>";
					echo "room type";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		echo "</fieldset>";
		
		$i = 0;
		while( $row = mysql_fetch_array( $result ) )
		{
			$visitor_room_no = $row['room_no'];
			$room_type = $row['room_type'];

			$visitFormName = "deleteroomform". $i;
			$visitsubmit = 'document.'. $visitFormName. '.submit()';
			echo "<fieldset>";
			echo "<legend>Leave Room</legend>";
			echo "<table>";
				echo "<tr>";
					echo "<td>";
						echo $visitor_room_no;
					echo "</td>";
					echo "<td>";
						echo $room_type;
					echo "</td>";
					echo "<td>";
						echo "<input type='button' value='visit room' onclick='".$visitsubmit."'/>";
					echo "</td>";
				echo "</tr>";
				
				echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' name='". $visitFormName. "'>";
					echo "<input type='hidden' name='func' value='visit_room'/>";
					echo "<input type='hidden' name='visitor_room_no' value='". $visitor_room_no ."'/>";
				echo "</form>";
			echo "</table>";
			echo "</fieldset>";
			$i++;
		}	
	}
	
	if( isset( $_POST['func'] ) && $_POST['func'] == 'dump_room' && isset( $_POST['visitor_room_no'] ) && isset( $_POST['visitor_libid'] ) )
	{
		$visitor_room_no =  $_POST['visitor_room_no'];
		$visitor_libid = $_POST['visitor_libid'];
		
		$query = "update Visitor set visitor_room_no=null where visitor_id='". $visitor_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		
		$query = "update Visitor set visitor_room_libid=null where visitor_id='". $visitor_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		
		echo "<script type=\"text/javascript\">"; 
			echo "alert('the room with number ". $visitor_room_no ." is dumped')";
		echo "</script>";
		
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
	}
	
	if( isset( $_POST['func'] ) && $_POST['func'] == 'visit_room' && isset( $_POST['visitor_room_no'] ) )
	{
		$visitor_room_no = $_POST['visitor_room_no'];
		$query = "update Visitor set visitor_room_no='". $visitor_room_no. "' where visitor_id='". $visitor_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		
		$query = "update Visitor set visitor_room_libid='". $library_id. "' where visitor_id='". $visitor_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		
		echo "<script type=\"text/javascript\">"; 
			echo "alert('the room with number ". $visitor_room_no ." is visited')";
		echo "</script>";
		
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
	}
	
	echo '</body></html>';
?>
