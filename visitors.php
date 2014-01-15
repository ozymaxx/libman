<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | Visitors');
	echo '<body>';

	$con = connectToSql();
	
	if(!isset($_SESSION['visitor_id']))
	{
		header( 'Location: index.php' );
	}
	
	$visitor_id = $_SESSION['visitor_id'];
	
	$query = "select * from Library natural join Visits where visitor_id='". $visitor_id. "' and auth_level='admin'";
	$result = mysql_query($query, $con) or die( mysql_error() );
	//$query = "select * from visits where visitor_id='". $visitor_id. "'";
	//$result = mysql_query($query, $con);
	echo "<h3>The Libraries you are in: </h3>";
	while($row = mysql_fetch_array($result))
	{
		$library_id = $row['library_id'];
		$auth_level = $row['auth_level'];
		$library_name = $row['library_name'];
		
		echo '<a href="logout.php">Logout</a><br />';
		echo "<a href='libmanager.php?lid=". $library_id. "'>".$library_name. " (". $auth_level.")</a><br />";
	}
	
	echo "<fieldset>";
	echo "<legend>Create a new library</legend>";
	echo "<form action=" .$_SERVER['PHP_SELF']." method='post'>";
		echo "<table>";
			echo "<tr>";
				echo "<td>library name</td>";
				echo "<td><input type='text' name='lib_name'/></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>library address</td>";
				echo "<td><textarea name='lib_addr'></textarea></td>";
			echo "</tr>";
			
			echo "<input type='hidden' name='add_lib' value='true'/>";
			
			echo "<tr>";
				echo "<input type='submit' value='create library' />";
			echo "</tr>";
		echo "</table>";
	echo "</form>";
	echo "</fieldset>";
	
	if( isset( $_POST['lib_name'] ) && isset( $_POST['lib_addr'] ) )
	{
		$lib_name = $_POST['lib_name'];
		$lib_addr = $_POST['lib_addr'];
		
		$query = "Insert into Library (library_name, library_addr) values ('$lib_name', '$lib_addr')";
		$result = mysql_query($query, $con) or die(mysql_error());
		
		$result = mysql_query("SHOW TABLE STATUS LIKE 'Library'", $con);
		$data = mysql_fetch_assoc($result) or die(mysql_error());
		$library_id = $data['Auto_increment'];
		$library_id = $library_id - 1;
		$level = 'admin';
		
		$query = "Insert into Visits values ('$library_id', '$visitor_id', '$level')";
		$result = mysql_query($query, $con) or die(mysql_error());
		
		echo "the id number for the new library that you have added is ". $library_id . "<br>";
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'" />';
	}
	
	if( isset( $_POST['deletelibrary'] ) && isset( $_POST['library_id'] ) )
	{
		$library_id = $_POST['library_id'];

		$query = "delete from Library where library_id='". $library_id. "'";
		$result = mysql_query($query, $con) or die(mysql_error());
		
		echo "deleted";
		echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'" />';
	}
	
	$query = "select * from Library natural join Visits where visitor_id='". $visitor_id. "'";
	$result = mysql_query($query, $con) or die(mysql_error());
	
	echo "<fieldset>";
	echo "<legend>Delete library</legend>";
		echo "<table>";
			echo "<tr>";
				echo "<td>library id</td>";
				echo "<td>library name</td>";
			echo "</tr>";
			$i = 0;
			while($row = mysql_fetch_array($result))
			{
				$library_id = $row['library_id'];
				$library_name = $row['library_name'];
				$formName = "deletelibraryform". $i;
				$submit = 'document.'. $formName. '.submit()';
				echo "<form action=" .$_SERVER['PHP_SELF']. " method='post' name='". $formName. "'>";
					echo "<input type='hidden' name='library_id' value='". $library_id."'/>";
					echo "<input type='hidden' name='deletelibrary' value='true'/>";
				echo "</form>";
			
				echo "<tr>";
					echo "<td>". $library_id. "</td>";
					echo "<td>". $library_name. "</td>";
					echo "<td>";
						echo "<input type='button' value='remove library' onclick='" .$submit ."'/>";
					echo "</td>";
				echo "</tr>";
				$i = $i + 1;
			}
			if( $i == 0 )
			{
				echo "no library to delete<br>";
			}
		echo "</table></fieldset>";
		
	echo '</body></html>';
?>
