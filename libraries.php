<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | General Library Management');
	echo '<body>';

	$con = connectToSql();
	
	if(!isset($_SESSION['visitor_id']))
	{
		header( 'Location: index.php' ) ;
	}
	
	$visitor_id = $_SESSION['visitor_id'];
	
	if( isset( $_GET['library_id'] ) )
	{
		$library_id = $_GET['library_id'];
		
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['library_id'].'">Back to The Library Menu</a>';
		
		$query = "select * from Visits where library_id='". $library_id. "' and visitor_id='". $visitor_id. "'";
		$result = mysql_query($query, $con) or die(mysql_error());
		
		$valid = false;
		while( $row = mysql_fetch_array($result) )
		{
			$auth_level = $row['auth_level'];
			$valid = true;
		}
		
		if( $valid == false )
		{
			header( 'Location: visitors.php' ) ;
		}
		
		if( $auth_level == 'admin' )
		{
			echo "<fieldset>";
			echo "<legend>Add a new user</legend>";
			echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post'>";
				echo "<table>";
					echo "<tr>";
						echo "<td>visitor first name</td>";
						echo "<td>";
							echo "<input type='text' name='visitor_first_name'/>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>visitor last name</td>";
						echo "<td>";
							echo "<input type='text' name='visitor_last_name'/>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>password</td>";
						echo "<td>";
							echo "<input type='password' name='visitor_pwd'/>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						//echo "<td>authentication level</td><td><input type='text' name='level1'/></td>";
						echo "<td>authentication level</td>";
						echo "<td><select name=level1>";
							echo "<option value='admin'>admin</option>";
							echo "<option value='only'>only</option>";
						echo "</select></td>";
					echo "</tr>";
					echo "<tr><input type='submit' value='send'/></tr>";
				echo "</table>";
			echo "</form>";
			echo "</fieldset>";
			
			echo "<fieldset>";
			echo "Add an existing user<br>";
			echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post'>";
				echo "<table>";
					echo "<tr>";
						echo "<td>User id </td><td><input type='text' name='id'/></td><br>";
					echo "</tr>";
					echo "<tr>";
						//echo "<td>authentication level</td><td><input type='text' name='level2'/></td>";
						echo "<td>authentication level</td>";
						echo "<td><select name=level2>";
							echo "<option value='admin'>admin</option>";
							echo "<option value='only'>only</option>";
						echo "</select></td>";
					echo "</tr>";
					echo "<tr><input type='submit' value='send'/></a></tr>";
				echo "</table>";
			echo "</form>";
			echo "</fieldset>";
			
			$query = "select * from Library natural join Shelf natural join Item where library_id='". $library_id. "'";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<fieldset>";
			echo "<legend>Add New Item</legend>";
			echo "<table>";
				echo "<tr>";
					echo "<form action=items.php method='get'>";
						echo "<tr><input type='submit' value='add book'/></a></tr>";
						echo "<input type='hidden' name='library_id' value='". $library_id. "'/>";
						echo "<input type='hidden' name='item_type' value='book'/>";
					echo "</form>";
					echo "<form action=items.php method='get'>";
						echo "<tr><input type='submit' value='add audio'/></a></tr>";
						echo "<input type='hidden' name='library_id' value='". $library_id. "'/>";
						echo "<input type='hidden' name='item_type' value='audio'/>";
					echo "</form>";
					echo "<form action=items.php method='get'>";
						echo "<tr><input type='submit' value='add video'/></a></tr>";
						echo "<input type='hidden' name='library_id' value='". $library_id. "'/>";
						echo "<input type='hidden' name='item_type' value='video'/>";
					echo "</form>";
				echo "</tr>";
			echo "</table>";
			echo "</fieldset>";
			
			echo "<fieldset>";
			echo "<legend>Delete item</legend>";
			echo "<table>";
				echo "<tr>";
					echo "<td>#</td>";
					echo "<td>Name</td>";
					echo "<td>Shelf Name</td>";
				echo "</tr>";
				$i = 0;
				while($row = mysql_fetch_array($result))
				{
					$item_id = $row['item_id'];
					$item_name = $row['item_name'];
					$shelf_name = $row['shelf_name'];
					$formName = "deleteitemform". $i;
					//$item_type = $row['shelf_type'];
					$submit = 'document.'. $formName. '.submit()';
					echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' name='". $formName. "'>";
						echo "<input type='hidden' name='item_id' value='". $item_id."'/>";
						echo "<input type='hidden' name='deleteitem' value='true'/>";
						//echo "<input type='hidden' name='item_type' value='". $item_type."'/>";
					echo "</form>";
				
					echo "<tr>";
						echo "<td>". $item_id. "</td>";
						echo "<td>". $item_name. "</td>";
						echo "<td>". $shelf_name. "</td>";
						echo "<td>";
							echo "<input type='button' value='remove item' onclick='" .$submit ."'/>";
						echo "</td>";
					echo "</tr>";
					$i = $i + 1;
				}
				if( $i == 0 )
				{
					echo "no item to delete<br>";
				}
			echo "</table>";
			echo "</fieldset>";
			
			echo "<fieldset>";
			echo "<legend>Add Section</legend>";
			echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' >";
				echo "<input type='hidden' name='add_section' value='true'/>";
				echo "<table>";
					echo "<tr>";
						echo "<td>";
							echo "section no";
						echo "</td>";
						echo "<td>";
							echo "<input type='number' min='0' name='section_no'/>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>";
							echo "section name";
						echo "</td>";
						echo "<td>";
							echo "<input type='text' name='section_name'/>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>";
							echo "<input type='submit' value='add section'/>";
						echo "</td>";
					echo "</tr>";
				echo "</table>";
			echo "</form>";
			echo "</fieldset>";
			
			$query = "select * from Section where library_id='". $library_id. "'";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<fieldset>";
			echo "<legend>Remove and Alter Section</legend>";
			echo "<table>";
				echo "<tr>";
					echo "<td>";
						echo "section no";
					echo "</td>";
					echo "<td>";
						echo "section name";
					echo "</td>";
				echo "</tr>";
				$i = 0;
				while( $row = mysql_fetch_array($result) )
				{
					$section_no = $row['section_no'];
					$section_name = $row['section_name'];
					$deleteFormName = "deletesectionform". $i;
					$alterFormName = "altersectionform". $i;
					//$item_type = $row['shelf_type'];
					$deletesubmit = 'document.'. $deleteFormName. '.submit()';
					$altersubmit = 'document.'. $alterFormName. '.submit()';
					
					echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' name='". $deleteFormName. "'>";
						echo "<input type='hidden' name='remove_section' value='true'/>";
						echo "<input type='hidden' name='section_no' value='". $section_no ."'/>";
						//echo "<input type='hidden' name='section_name'". $section_name .">";
					echo "</form>";
					
					echo "<form action=shelves.php method='get' name='". $alterFormName. "'>";
						echo "<input type='hidden' name='library_id' value='". $library_id ."'/>";
						echo "<input type='hidden' name='section_no' value='". $section_no ."'/>";
						//echo "<input type='hidden' name='section_name'". $section_name .">";
					echo "</form>";
					
					echo "<tr>";
						echo "<td>";
							echo $section_no;
						echo "</td>";
						echo "<td>";
							echo $section_name;
						echo "</td>";
						echo "<td>";
							echo "<input type='button' value='remove section' onclick='".$deletesubmit."'/>";
						echo "</td>";
						echo "<td>";
							echo "<input type='button' value='add-remove shelves' onclick='".$altersubmit."'/>";
						echo "</td>";
					echo "</tr>";
					$i = $i + 1;
				}
			echo "</table>";
			echo "</fieldset>";
			
			$query = "select * from Visitor natural join Visits where library_id='". $library_id. "'";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<fieldset>";
			echo "<legend>Delete User From This Library</legend>";
			echo "<table>";
				echo "<tr>";
					echo "<td>id</td><td>name</td><td>last name</td>";
				echo "</tr>";
				$i = 0;
				while($row = mysql_fetch_array($result))
				{
					$visitor_id = $row['visitor_id'];
					$visitor_name = $row['visitor_first_name'];
					$visitor_last_name = $row['visitor_last_name'];
					$formName = "form". $i;
					$submit = 'document.'. $formName. '.submit()';
					
					echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. " method='post' name='". $formName. "'>";
						echo "<input type='hidden' name='visitor_id' value='". $visitor_id."'/>";
						echo "<input type='hidden' name='deletevisitor' value='true'/>";
					echo "</form>";
					
					echo "<tr>";
						echo "<td>";
							echo $visitor_id;
						echo "</td>";
						echo "<td>";
							echo $visitor_name;
						echo "</td>";
						echo "<td>";
							echo $visitor_last_name;
						echo "</td>";
						if( $visitor_id != $_SESSION['visitor_id'] )
						{
							echo "<td>";
								echo "<input type='button' value='remove visitor' onclick='" .$submit ."'/>";
							echo "</td>";
						}
					echo "</tr>";
					
					$i = $i + 1;
				}
			echo "</table>";
			echo "</fieldset>";
			
			if( isset( $_POST['remove_section'] ) && isset( $_POST['section_no'] )  )
			{
				echo "remove detected<br>";
				$section_no = $_POST['section_no'];
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert(".$section_name.")";
				echo "</script>"; 
				echo $section_no;
				$query = "delete from Section where section_no='". $section_no. "' and library_id='". $library_id. "'";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('section number". $section_no ."deleted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
			
			if( isset( $_POST['add_section'] ) && isset( $_POST['section_no'] ) && isset( $_POST['section_name'] ) )
			{
				$section_no = $_POST['section_no'];
				$section_name = $_POST['section_name'];
				
				$query = "select * from Section where section_no='". $section_no. "' and library_id='". $library_id. "'";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				$exist = false;
				while( $row = mysql_fetch_array($result) )
				{
					$exist = true;
					echo "<script type=\"text/javascript\">"; 
						echo "alert('section number ". $section_no ." already exists')";
					echo "</script>";
				}
				
				if( $exist == false )
				{
					$query = "insert into Section values ('$section_no', '$library_id', '$section_name')"; 
					$result = mysql_query($query, $con) or die(mysql_error());
					
					echo "<script type=\"text/javascript\">"; 
						echo "alert('section with number ". $section_no ." inserted')";
					echo "</script>";
				}
				
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
			
			if( isset( $_POST['item_id'] ) && isset( $_POST['deleteitem'] ) )
			{
				$item_id = $_POST['item_id'];
				
				$query = "delete from Item where item_id='". $item_id. "'";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('item with id ". $item_id ." deleted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
			
			if( isset( $_POST['visitor_id'] ) && isset( $_POST['deletevisitor'] ) )
			{
				$visitor_id = $_POST['visitor_id'];
				$query = "delete from Visits where library_id='". $library_id. "' and visitor_id='". $visitor_id. "'";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('visitor with id ". $visitor_id ." deleted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
			
			if( isset( $_POST['visitor_first_name'] ) && isset( $_POST['visitor_last_name'] ) && isset( $_POST['visitor_pwd'] ) && isset( $_POST['level1'] )  )
			{
				$level = $_POST['level1'];
				$fn = $_POST['visitor_first_name'];
				$ln = $_POST['visitor_last_name'];
				$pw = $_POST['visitor_pwd'];
				$query = "Insert into Visitor (visitor_pwd, visitor_first_name, visitor_last_name) values ('$pw', '$fn', '$ln')";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				$result = mysql_query("SHOW TABLE STATUS LIKE 'Visitor'", $con);
				$data = mysql_fetch_assoc($result) or die(mysql_error());
				$visitor_id = $data['Auto_increment'];
				$visitor_id = $visitor_id - 1;
				
				$query = "Insert into Visits values ('$library_id', '$visitor_id', '$level')";
				$result = mysql_query($query, $con) or die(mysql_error());
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('a new visitor with id ". $visitor_id ." inserted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
			
			if( isset( $_POST['id'] ) && isset( $_POST['level2'] ) )
			{
				$id = $_POST['id'];
				$level = $_POST['level2'];
				/*
				$list = mysql_query( "SELECT * FROM Visits where library_id='". $library_id. " and visitor_id='". $visitor_id. "'" );
				if ( mysql_num_rows( $list ) == 0 )
				{
					$query = "Insert into visits values ('$library_id', '$id', '$level')";
					$result = mysql_query($query, $con) or die("could not be added");
					
					echo "<script type=\"text/javascript\">"; 
						echo "alert('visitor with id ". $id ."now is a vistor of the library with id". $library_id ."')";
					echo "</script>";
				}
				else{
					echo "<script type=\"text/javascript\">"; 
						echo "alert('visitor with id ". $id ."is already exists in the library with id ". $library_id ."')";
					echo "</script>";
				}
				*/
				
				$query = "select * from Visitor where visitor_id='". $id. "'";
				$result = mysql_query( $query, $con );
				
				if(  mysql_fetch_row( $result ) == 0 )
				{
					echo "<script type=\"text/javascript\">"; 
						echo "alert('visitor with id ". $id ."is a visitor of no library')";
					echo "</script>";
				}
				else
				{
					$query = "Insert into Visits values ('$library_id', '$id', '$level')";
					if(  mysql_query($query, $con) )
					{
						echo "<script type=\"text/javascript\">"; 
							echo "alert('visitor with id ". $id ."now is a visitor of the library with id". $library_id ."')";
						echo "</script>";
					}
					else
					{
						echo "<script type=\"text/javascript\">"; 
							echo "alert('visitor with id ". $id ."is already exists in the library with id ". $library_id ."')";
						echo "</script>";
					}
				}
								
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'" />';
			}
		}
	}
	else
	{
		echo "You should select a library to connect this page go back to <a href='visitors.php'>visitor page</a>";
	}
	
	echo '</body></html>';
	//mysql_query($con, "Insert into visitor (visitor_id, visitor_pwd, visitor_first_name, visitor_last_name, visitor_room_no, 
	//	visitor_room_start_time, visitor_room_end_time) values ()")
?>
