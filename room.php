<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | Room Management');
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
	
	$query = "select * from Visits where library_id='". $library_id. "' and visitor_id='". $visitor_id. "' and auth_level='admin'";
	$result = mysql_query( $query, $con ) or die( mysql_error() );
	
	$exist = false;
	while( $row = mysql_fetch_array( $result ) )
	{
		$exist = true;
	}
	
	if( $exist == false )
	{
		header( 'Location: visitors.php' ) ;
	}
	
	echo '<a href="logout.php">Logout</a><br />';
	echo '<a href="visitors.php">Back To Main Libraries Screen Menu</a><br />';
	echo "<fieldset>";
	echo "<legend>Add New Room</legend>";
	echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id." method='post' >";
		echo "<input type='hidden' name='func' value='add_room'/>";
		echo "<table>";
			echo "<tr>";
				echo "<td>";
					echo "room no";
				echo "</td>";
				echo "<td>";
					echo "<input type='number' name='room_no' min='0' />";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "capacity";
				echo "</td>";
				echo "<td>";
					echo "<input type='number' name='capacity' min='0' />";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "room type";
				echo "</td>";
				echo "<td>";
					echo "<select name='room_type'>";
						echo "<option value='media'>media</option>";
						echo "<option value='study'>study</option>";
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<input type='submit' value='add room'>";
			echo "</tr>";
		echo "</table>";
	echo "</form>";
	echo "</fieldset>";
	
	$query = "select * from Room where library_id='". $library_id. "'";
	$result = mysql_query( $query, $con ) or die( mysql_error() );
	
	echo "<fieldset>";
	echo "<legend>Remove Room</legend>";
	echo "<table>";
		echo "<tr>";
			echo "<td>";
				echo "room_no";
			echo "</td>";
			echo "<td>";
				echo "room_type";
			echo "</td>";
		echo "</tr>";
		
		$i = 0;
		while( $row = mysql_fetch_array( $result ) )
		{
			$room_no = $row['room_no'];
			$room_type = $row['room_type'];
			
			$deleteFormName = "deleteroomform". $i;
			$deletesubmit = 'document.'. $deleteFormName. '.submit()';
			
			echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' name='". $deleteFormName. "'>";
				echo "<input type='hidden' name='func' value='remove_room'/>";
				echo "<input type='hidden' name='room_no' value='". $room_no ."'/>";
			echo "</form>";
			
			echo "<tr>";
				echo "<td>";
					echo $room_no;
				echo "</td>";
				echo "<td>";
					echo $room_type;
				echo "</td>";
				echo "<td>";
					echo "<input type='button' value='remove room' onclick='".$deletesubmit."'/>";
				echo "</td>";
			echo "</tr>";
			
			$i++;
		}
	echo "</table>";
	echo "</fieldset>";
	
	if( isset( $_POST['func'] ) && $_POST['func'] == 'remove_room' && isset( $_POST['room_no'] ) )
	{
		$room_no = $_POST['room_no'];
		
		$query = "delete from Room where library_id='". $library_id. "' and room_no='". $room_no. "'";
		$result = mysql_query( $query, $con ) or die( mysql_error() );
		
		echo "<script type=\"text/javascript\">"; 
			echo "alert('the new room with number ". $room_no ." is removed from the library with id". $library_id ."')";
		echo "</script>";
		
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
	}
	
	if( isset( $_POST['func'] ) && $_POST['func'] == 'add_room' && isset( $_POST['room_no'] )  
		&& isset( $_POST['capacity'] ) && isset( $_POST['room_type'] ) )
	{
		echo "enter";
		$room_no = $_POST['room_no'];
		$capacity = $_POST['capacity'];
		$room_type = $_POST['room_type'];
		
		$query = "select * from Room where library_id='". $library_id. "' and room_no='". $room_no. "'";
		$result = mysql_query( $query, $con ) or die( mysql_error() );
		
		$exist = false;
		while( $row = mysql_fetch_array( $result ) )
		{
			$exist = true;
		}
		
		if( $exist == false )
		{
			$query = "insert into Room values ( '$room_no', '$library_id', '$capacity', '$room_type', 0 )";
			$result = mysql_query( $query, $con ) or die( mysql_error() );
			
			echo "<script type=\"text/javascript\">"; 
				echo "alert('a new room with number ". $room_no ."now is added to the library with id". $library_id ."')";
			echo "</script>";
		}
		else
		{
			echo "<script type=\"text/javascript\">"; 
				echo "alert('a new room with number ". $room_no ." is already exists in the library with id". $library_id ."')";
			echo "</script>";
		}
		
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
	}
	echo '</body></html>';
?>
