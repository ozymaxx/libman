<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	
	display_page_head( 'LibMan | Items');
	echo '<body>';

	$con = connectToSql();
	
	if( !isset( $_GET['item_type'] ) || !isset( $_SESSION['visitor_id'] ) || !isset( $_GET['library_id'] ) )
	{
		header( 'Location: index.php' ) ;
	}
	
	echo '<a href="logout.php">Logout</a><br />';
	echo '<a href="libmanager.php?lid='.$_GET['library_id'].'">Back to The Library Menu</a>';
	
	$item_type = $_GET['item_type'];
	$visitor_id = $_SESSION['visitor_id'];
	$library_id = $_GET['library_id'];
	
	$query = "select * from Visits where visitor_id='". $visitor_id. "' and auth_level='admin' and library_id='". $library_id. "'";
	$result = mysql_query($query, $con) or die("select libraries error");
	
	
	$valid = false;
	while($row = mysql_fetch_array($result))
	{
		$valid = true;
	}
	
	if( $valid == false )
	{
		header( 'Location: index.php' );
	}
	
	echo "<form action=" .$_SERVER['PHP_SELF'].	"?library_id=". $library_id . "&item_type=". $item_type." method='post' enctype='multipart/form-data'>";
		//echo "<input type='hidden' name='item_type' value='". $item_type. "'/>";
		$add_cond = true;
		echo "<input type='hidden' name='add_item' value='". $add_cond. "'/>";
		echo "<table>";
			echo "<tr>";
				echo "<td>";
					echo "item name";
				echo "</td>";
				echo "<td>";
					echo "<input type='text' name='item_name'/>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "picture address";
				echo "</td>";
				echo "<td>";
					echo "<input type='file' name='picture_addr'/>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "publication date";
				echo "</td>";
				echo "<td>";
					echo "<input type='text' name='publication_date'/>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "shelf id ------ name";
				echo "</td>";
				echo "<td>";
					echo "<select name='shelf_id'>";
					
					$query = "select * from Shelf where library_id='". $library_id. "' and shelf_type='". $item_type. "'";
					$result = mysql_query($query, $con) or die("item select query problem");
					
					while( $row = mysql_fetch_array($result) )
					{
						$shelf_id = $row['shelf_id'];
						$shelf_name = $row['shelf_name'];
						echo "<option value='". $shelf_id. "'>". $shelf_id. "-------". $shelf_name. "</option>";
					}
					
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			
			if( $item_type == 'book' )
			{
				echo "<tr>";
					echo "<td>";
						echo "publisher";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='publisher'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "authors";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='authors'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "page count";
					echo "</td>";
					echo "<td>";
						echo "<input type='number' name='page_count' min='0' value='0'/>";
					echo "</td>";
				echo "</tr>";
			}
			else if( $item_type == 'audio' || $item_type == 'video' )
			{
				echo "<tr>";
					echo "<td>";
						echo "duration";
					echo "</td>";
					echo "<td>";
						echo "<input type='time' name='duration'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "recorder first name";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='recorder_first_name'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "recorder last name";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='recorder_last_name'/>";
					echo "</td>";
				echo "</tr>";
			} 
			if( $item_type == 'video' )
			{
				echo "<tr>";
					echo "<td>";
						echo "actors";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='actors'/>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>";
						echo "directors";
					echo "</td>";
					echo "<td>";
						echo "<input type='text' name='directors'/>";
					echo "</td>";
				echo "</tr>";
			}
			
			echo "<tr>";
				echo "<input type='submit' value='add item'/>";
			echo "</tr>";		
		echo "</table>";
	echo "</form>";
	
	if( isset( $_POST['add_item'] ) && isset( $_POST['item_name'] ) && isset( $_POST['publication_date'] ) && isset( $_FILES['picture_addr'] ) && isset( $_POST['shelf_id'] ) )
	{
		$item_name = $_POST['item_name'];
		$publication_date = $_POST['publication_date'];
		$shelf_id = $_POST['shelf_id'];
		//$picture_address = "";
		$picture_address = "";
		if ($_FILES["picture_addr"]["error"] <= 0)
		{
			$picture_address = $_FILES['picture_addr']['name'];
			$lastpos = strripos($picture_address, ".");
			$ext = substr($picture_address ,$lastpos + 1);
			
			if($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "JPG")
			{
				$original = substr($picture_address, 0, $lastpos);
				//$temp = $original;
				$count = 1;
				
				while(true)
				{
					if (!file_exists("item-photos/". $count."." .$ext ))
					{  
						move_uploaded_file($_FILES["picture_addr"]["tmp_name"], "item-photos/". $count."." .$ext);
						$picture_address = $count."." .$ext;
						break;
					}
					else
					{
						$count++;
					}
				}
			}
		}
		
		$query = "select * from Item where shelf_id='". $shelf_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		$num = mysql_num_rows( $result );
		
		$query = "select * from Shelf where shelf_id='". $shelf_id. "'";
		$result = mysql_query( $query ) or die( mysql_error() );
		
		$capacity = 0;
		
		//$capacity = $result['capacity'];
		
		while( $row = mysql_fetch_array( $result ) )
		{
			$capacity = (int)($row['capacity']);
		}
		
		$full = $capacity <= $num;
		
		$full = $capacity <= $num;
		
		if( $full == true )
			//if( true )
		{
			echo "<script type=\"text/javascript\">"; 
				echo "alert('shelf is full ".$capacity."  ".$num ."')";
			echo "</script>";
			echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?item_type='.$item_type.'&library_id='.$library_id.'" />';
		}
		
		if( $item_type == 'book' && isset( $_POST['publisher'] ) && isset( $_POST['page_count'] ) && isset( $_POST['authors'] ) )
		{
			if( $full == false )
			{
				$publisher = $_POST['publisher'];
				$page_count = $_POST['page_count'];
				$authors = $_POST['authors'];
				
				$query = "Insert into Item (item_name, picture_addr, borrow_count, reserve_count, publication_date, shelf_id) 
					values ('$item_name', '$picture_address', 0, 0, '$publication_date', '$shelf_id')";
				$result = mysql_query($query, $con) or die("book add item error");
				
				$result = mysql_query("SHOW TABLE STATUS LIKE 'Item'", $con);
				$data = mysql_fetch_assoc($result) or die("error");
				$item_id = $data['Auto_increment'];
				$item_id = $item_id - 1;
				
				$query = "Insert into Book (item_id, publisher, page_count) 
					values ('$item_id', '$publisher', '$page_count')";
				$result = mysql_query($query, $con) or die("book add book error");
				
				$author_array = explode( ",", $authors );
				for( $i = 0; $i < count( $author_array ); $i++)
				{
					$author_name = $author_array[$i];
					$names = explode( " ", $author_name );
					$author_first_name = "";
					
					for( $j = 0; $j < count( $names ) - 1; $j++ )
					{
						$author_first_name .= $names[$j] . " ";
					}
					
					$author_last_name = $names[ count( $names ) - 1 ];
					
					$query = "Insert into Author (author_first_name, author_last_name) 
						values ('$author_first_name', '$author_last_name')";
					$result = mysql_query($query, $con) or die("book add author error");
					
					$result = mysql_query("SHOW TABLE STATUS LIKE 'Author'", $con);
					$data = mysql_fetch_assoc($result) or die("error");
					$author_id = $data['Auto_increment'];
					$author_id = $author_id - 1;
					
					$query = "Insert into Is_Written_By (author_id, book_id) 
						values ('$author_id', '$item_id')";
					$result = mysql_query($query, $con) or die("book written by insert error");
				}
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('a new book with id ". $item_id ." is inserted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?item_type='.$item_type.'&library_id='.$library_id.'" />';
			}
		}
		else if( $item_type == 'video' && isset( $_POST['duration'] ) && isset( $_POST['recorder_first_name'] ) 
			&& isset( $_POST['recorder_last_name'] ) && isset( $_POST['actors'] ) && isset( $_POST['directors'] ) )
		{
			if( full == false )
			{
				$duration = $_POST['duration'];
				$recorder_first_name = $_POST['recorder_first_name'];
				$recorder_last_name = $_POST['recorder_last_name'];
				$actors = $_POST['actors'];
				$directors = $_POST['directors'];
				
				$query = "Insert into Item (item_name, picture_addr, borrow_count, reserve_count, publication_date, shelf_id) 
					values ('$item_name', '$picture_address', 0, 0, '$publication_date', '$shelf_id')";
				$result = mysql_query($query, $con) or die("audio add item error");
				
				$result = mysql_query("SHOW TABLE STATUS LIKE 'Item'", $con);
				$data = mysql_fetch_assoc($result) or die("error");
				$item_id = $data['Auto_increment'];
				$item_id = $item_id - 1;
				
				$query = "insert into Recorder(recorder_first_name, recorder_last_name) values ('$recorder_first_name', '$recorder_last_name')";
				$result = mysql_query($query, $con) or die("recorder insert error");
				
				$result = mysql_query("SHOW TABLE STATUS LIKE 'Recorder'", $con);
				$data = mysql_fetch_assoc($result) or die("error");
				$recorder_id = $data['Auto_increment'];
				$recorder_id = $recorder_id - 1;
				
				
				$query = "Insert into Video (item_id, duration, recorder_id) 
					values ('$item_id' ,'$duration', '$recorder_id')";
				$result = mysql_query($query, $con) or die("audio add audio error");
				
				$actor_array = explode( ",", $actors );
				for( $i = 0; $i < count( $actor_array ); $i++)
				{
					$actor_name = $actor_array[$i];
					$names = explode( " ", $actor_name );
					$actor_first_name = "";
					
					for( $j = 0; $j < count( $names ) - 1; $j++ )
					{
						$actor_first_name .= $names[$j] . " ";
					}
					
					$actor_last_name = $names[ count( $names ) - 1 ];
					
					$query = "select * from Actor where actor_first_name='". $actor_first_name. "' and actor_last_name='". $actor_last_name. "'";
					$result = mysql_query($query, $con) or die("audio select error");
					
					$exists = false;
					$actor_id = 0;
					
					while($row = mysql_fetch_array($result))
					{
						$actor_id = $row['actor_id'];
						$exists = true;
					}
					
					if( $exists == false )
					{
						$query = "Insert into Actor (actor_first_name, actor_last_name) 
							values ('$actor_first_name', '$actor_last_name')";
						$result = mysql_query($query, $con) or die(mysql_error());
						
						$result = mysql_query("SHOW TABLE STATUS LIKE 'Actor'", $con);
						$data = mysql_fetch_assoc($result) or die("error");
						$actor_id = $data['Auto_increment'];
						$actor_id = $actor_id - 1;
					}
					
					$director_array = explode( ",", $directors );
					for( $k = 0; $k < count( $director_array ); $k++)
					{
						$director_name = $director_array[$k];
						$director_names = explode( " ", $director_name );
						$director_first_name = "";
						
						for( $z = 0; $z < count( $director_names ) - 1; $z++ )
						{
							$director_first_name .= $director_names[$z] . " ";
						}
						
						$director_last_name = $director_names[ count( $director_names ) - 1 ];
						
						$query = "select * from Director where director_first_name='". $director_first_name. "' and director_last_name='". $director_last_name. "'";
						$result = mysql_query($query, $con) or die("audio select error");
						
						$exists = false;
						$director_id = 0;
						
						while($row = mysql_fetch_array($result))
						{
							$director_id = $row['director_id'];
							$exists = true;
						}
						
						if( $exists == false )
						{
							$query = "Insert into Director (director_first_name, director_last_name) 
								values ('$director_first_name', '$director_last_name')";
							$result = mysql_query($query, $con) or die(mysql_error());
							
							$result = mysql_query("SHOW TABLE STATUS LIKE 'Director'", $con);
							$data = mysql_fetch_assoc($result) or die("error");
							$director_id = $data['Auto_increment'];
							$director_id = $director_id - 1;
						}
						
						$query = "select * from is_produced_by where actor_id='". $actor_id. "' and director_id='". $director_id. "' and item_id='". $item_id. "'";
						$result = mysql_query( $query ) or die( mysql_error() );
						
						if( mysql_num_rows( $result ) == 0 )
						{
							$query = "Insert into is_produced_by (actor_id, director_id, item_id) 
							values ('$actor_id', $director_id, '$item_id')";
							$result = mysql_query($query, $con) or die( mysql_error() );
						}
						
						$query = "select * from directs where actor_id='". $actor_id. "' and director_id='". $director_id. "'";
						$result = mysql_query( $query ) or die( mysql_error() );
						
						if( mysql_num_rows( $result ) == 0 )
						{
							$query = "Insert into Directs (actor_id, director_id) 
							values ('$actor_id', $director_id)";
							$result = mysql_query($query, $con) or die( mysql_error() );
						}
					}
			}
			
			echo "<script type=\"text/javascript\">"; 
				echo "alert('a new video with id ". $item_id ." is inserted')";
			echo "</script>";
			echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?item_type='.$item_type.'&library_id='.$library_id.'" />';
			}
		}
		else if( $item_type == 'audio' && isset( $_POST['duration'] ) && isset( $_POST['recorder_first_name'] ) && isset( $_POST['recorder_last_name'] ) )
		{
			if( $full == false )
			{
				$duration = $_POST['duration'];
				$recorder_first_name = $_POST['recorder_first_name'];
				$recorder_last_name = $_POST['recorder_last_name'];
				
				$query = "Insert into item (item_name, picture_addr, borrow_count, reserve_count, publication_date, shelf_id) 
					values ('$item_name', '$picture_address', 0, 0, '$publication_date', '$shelf_id')";
				$result = mysql_query($query, $con) or die("video add item error");
				
				$result = mysql_query("SHOW TABLE STATUS LIKE 'item'", $con);
				$data = mysql_fetch_assoc($result) or die("error");
				$item_id = $data['Auto_increment'];
				$item_id = $item_id - 1;
				
				$query = "select * from recorder where recorder_first_name='". $recorder_first_name. "' and recorder_last_name='". $recorder_last_name. "'";
				$result = mysql_query($query, $con) or die("audio select error");
				
				$exists = false;
				$recorder_id = 0;
				
				while($row = mysql_fetch_array($result))
				{
					$recorder_id = $row['recorder_id'];
					$exists = true;
				}
				
				if( $exists == false )
				{
					$query = "insert into recorder(recorder_first_name, recorder_last_name) values ('$recorder_first_name', '$recorder_last_name')";
					$result = mysql_query($query, $con) or die("recorder insert error");
					
					$result = mysql_query("SHOW TABLE STATUS LIKE 'recorder'", $con);
					$data = mysql_fetch_assoc($result) or die("error");
					$recorder_id = $data['Auto_increment'];
					$recorder_id = $recorder_id - 1;
				}
				
				$query = "Insert into audio (item_id, duration, recorder_id) 
					values ('$item_id' ,'$duration', '$recorder_id')";
				$result = mysql_query($query, $con) or die("video add video error");
				
				echo "<script type=\"text/javascript\">"; 
					echo "alert('a new audio with id ". $item_id ." is inserted')";
				echo "</script>";
				echo '<meta http-equiv="refresh" content="1; url="'.$_SERVER['PHP_SELF'].'?item_type='.$item_type.'&library_id='.$library_id.'" />';
			}
		}
	}
	
	echo "change item type to add<br>";
	if( $item_type != 'book' )
		echo "<a href='". $_SERVER['PHP_SELF']. "?item_type=book&library_id=". $library_id. "'>book</a><br>";
	if( $item_type != 'audio' )
		echo "<a href='". $_SERVER['PHP_SELF']. "?item_type=audio&library_id=". $library_id. "'>audio</a><br>";
	if( $item_type != 'video' )
		echo "<a href='". $_SERVER['PHP_SELF']. "?item_type=video&library_id=". $library_id. "'>video</a><br>";
		
	echo '</body></html>';
?>
