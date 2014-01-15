<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | Shelves');
	echo '<body>';
	
	$con = connectToSql();
	
	if( isset( $_GET['library_id'] ) && isset( $_GET['section_no'] ) )
	{
		$library_id = $_GET['library_id'];
		$section_no = $_GET['section_no'];
		
		echo '<a href="logout.php">Logout</a><br />';
		echo '<a href="libmanager.php?lid='.$_GET['library_id'].'">Back to The Library Menu</a>';
		
		//echo "add shelf";
		echo "<fieldset>";
		echo "<legend>Add Shelf</legend>";
		echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. "&section_no=". $section_no ." method='post' >";
			echo "<input type='hidden' name='method_name' value='add_shelf' />";
			echo "<table>";
				echo "<tr>";
					echo "<td>";
						echo "shelf name";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='shelf_name' />";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "capacity";
					echo "</td>";
					echo "<td>";
						echo "<input type='number' name='capacity' min='0'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "shelf type";
					echo "</td>";
					echo "<td>";
						echo "<select name='shelf_type' >";
							echo "<option value='book'>book</option>";
							echo "<option value='audio'>audio</option>";
							echo "<option value='video'>video</option>";
						echo "</select>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<input type='submit' value='add shelf'>";
				echo "</tr>";
			echo "</table>";
		echo "</form>";
		echo "</fieldset>";
		
		$query = "select * from Shelf where section_no='". $section_no. "' and library_id='". $library_id. "'";
		$result = mysql_query($query, $con) or die( mysql_error() );
		echo "remove shelf";
		echo "<table>";
			echo "<tr>";
				echo "<td>";
					echo "shelf id";
				echo "</td>";
				echo "<td>";
					echo "shelf name";
				echo "</td>";
				echo "<td>";
					echo "capacity";
				echo "</td>";
				echo "<td>";
					echo "shelf type";
				echo "</td>";
			echo "</tr>";
			
			$i = 0;
			
			while( $row = mysql_fetch_array( $result ) )
			{
				$shelf_id = $row['shelf_id'];
				$shelf_name = $row['shelf_name'];
				$capacity = $row['capacity'];
				$shelf_type = $row['shelf_type'];
				$formName = "removeshelfform". $i;
				$submit = 'document.'. $formName. '.submit()';
				
				echo "<form action=" .$_SERVER['PHP_SELF']. "?library_id=". $library_id. "&section_no=". $section_no." method='post' name='". $formName. "'>";
					echo "<input type='hidden' name='shelf_id' value='". $shelf_id."'/>";
					echo "<input type='hidden' name='method_name' value='remove_shelf'/>";
				echo "</form>";
				
				echo "<tr>";
					echo "<td>";
						echo $shelf_id;
					echo "</td>";
					echo "<td>";
						echo $shelf_name;
					echo "</td>";
					echo "<td>";
						echo $capacity;
					echo "</td>";
					echo "<td>";
						echo $shelf_type;
					echo "</td>";
					echo "<td>";
						echo "<input type='button' value='remove shelf' onclick='" .$submit ."'/>";
					echo "</td>";
				echo "</tr>";
				
				$i = $i + 1;
			}
		echo "</table>";
		
		if( isset( $_POST['method_name'] ) && $_POST['method_name'] == 'add_shelf' && isset( $_POST['shelf_name'] ) 
			&& isset( $_POST['capacity'] ) && isset( $_POST['shelf_type'] ) )
		{
			$shelf_name = $_POST['shelf_name'];
			$capacity = $_POST['capacity'];
			$shelf_type = $_POST['shelf_type'];
			
			//$query = "insert into section values ('$section_no', '$library_id', '$section_name')"; 
			$query = "insert into Shelf(shelf_name, capacity, shelf_type, section_no, library_id) values
				('$shelf_name', '$capacity', '$shelf_type', '$section_no', '$library_id')";
			$result = mysql_query($query, $con) or die( mysql_error() );
			
			$result = mysql_query("SHOW TABLE STATUS LIKE 'shelf'", $con);
			$data = mysql_fetch_assoc($result) or die(mysql_error());
			$shelf_id = $data['Auto_increment'];
			$shelf_id = $shelf_id - 1;
			
			echo "<script type=\"text/javascript\">"; 
				echo "alert('the lastly added self id is ". $shelf_id ."')";
			echo "</script>";
			echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'&section_no='.$section_no.'" />';
		}
		
		if( isset( $_POST['method_name'] ) && $_POST['method_name'] == 'remove_shelf' && isset( $_POST['shelf_id'] ) )
		{
			$shelf_id = $_POST['shelf_id'];
			
			$query = "delete from Shelf where shelf_id='". $shelf_id. "'";
			$result = mysql_query($query, $con) or die( mysql_error() );
			
			echo "<script type=\"text/javascript\">"; 
				echo "alert('shelf with id". $shelf_id ."deleted')";
			echo "</script>";
			echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?library_id='.$library_id.'&section_no='.$section_no.'" />';
		}
	}
	
	echo '</body></html>';
?>
