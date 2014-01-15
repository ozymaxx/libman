<?php
	include_once( './lib/sql_connection.php');
	include_once( './lib/funcs.php');

	session_start();
	display_page_head( 'LibMan | Statistics');
	echo '<body>';
	
	$con = connectToSql();
	
	echo '<a href="logout.php">Logout</a><br />';
	echo '<a href="visitors.php">Back to The Libraries Selection Menu</a><br>';
	
	if( !isset( $_SESSION['visitor_id'] ) )
	{
		header( 'Location: index.php' ) ;
	}
	$visitor_id = $_SESSION['visitor_id'];
	$query = "select * from Visits where visitor_id='". $visitor_id. "'";
	$result = mysql_query( $query, $con );
	
	if( mysql_num_rows( $result ) == 0 )
	{
		//echo mysql_num_rows( $result );
		header( 'Location: visitors.php' ) ;
	}
	
	echo "<a href=statistics.php?id=1>mostly borrowed books</a><br>";
	echo "<a href=statistics.php?id=2>mostly reserved books</a><br>";
	echo "<a href=statistics.php?id=3>mostly borrowed audios</a><br>";
	echo "<a href=statistics.php?id=4>mostly reserved audios</a><br>";
	echo "<a href=statistics.php?id=5>mostly borrowed videos</a><br>";
	echo "<a href=statistics.php?id=6>mostly reserved videos</a><br>";
	echo "<a href=statistics.php?id=7>mostly used rooms</a><br>";
	echo "<a href=statistics.php?id=8>list the libraries according to the # of visitors</a><br>";
	echo "<a href=statistics.php?id=9>mostly watched directors</a><br>";
	echo "<a href=statistics.php?id=10>mostly watched recorders(video)</a><br>";
	echo "<a href=statistics.php?id=11>mostly watched recorders(audio)</a><br>";
	echo "<a href=statistics.php?id=12>mostly preferred hour policies</a><br>";
	
	if( isset( $_GET['id'] ) )
	{
		$id = $_GET['id'];
		
		if( $id == 1 )
		{
			$query = "select * from (Book natural join Item) where borrow_count=(select max(borrow_count) from (Book natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['borrow_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 2 )
		{
			$query = "select * from (Book natural join Item) where reserve_count=(select max(reserve_count) from (Book natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['reserve_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 3 )
		{
			$query = "select * from (Audio natural join Item) where borrow_count=(select max(borrow_count) from (Audio natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['borrow_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 4 )
		{
			$query = "select * from (Audio natural join Item) where reserve_count=(select max(reserve_count) from (Audio natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['reserve_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 5 )
		{
			$query = "select * from (Video natural join Item) where borrow_count=(select max(borrow_count) from (Video natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['borrow_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 6 )
		{
			$query = "select * from (Video natural join Item) where reserve_count=(select max(reserve_count) from (Video natural join Item) )";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$item_name = $row['item_name'];
					$count = $row['reserve_count'];
					echo "<tr>";
						echo "<td>";
							echo $item_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 7 )
		{
			$query = "select * from Room where visit_count=(select max(visit_count) from Room)";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>library id</td>";
					echo "<td>room no</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$library_id = $row['library_id'];
					$room_no = $row['room_no'];
					$count = $row['visit_count'];
					echo "<tr>";
						echo "<td>";
							echo $library_id;
						echo "</td>";
						echo "<td>";
							echo $room_no;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 8 )
		{
			$query = "select library_id,library_name,count(visitor_id) as count from (Library natural join Visits) group by library_id order by count(visitor_id)";
			$result = mysql_query($query, $con) or die(mysql_error());
			
			echo "<table>";
				echo "<tr>";
					echo "<td>library id</td>";
					echo "<td>library name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$library_id = $row['library_id'];
					$library_name = $row['library_name'];
					$count = $row['count'];
					echo "<tr>";
						echo "<td>";
							echo $library_id;
						echo "</td>";
						echo "<td>";
							echo $library_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 9 )
		{
			$query = "select director_id, director_first_name, director_last_name, sum(borrow_count+reserve_count) as count from Director natural join 
				Is_Produced_By natural join Item group by director_id having sum( borrow_count + reserve_count ) >= 
				all (select sum( borrow_count + reserve_count ) from director natural join is_produced_by natural join item group by director_id)";
			
			$result = mysql_query($query, $con) or die("error");
			
			echo "<table>";
				echo "<tr>";
					echo "<td>director id</td>";
					echo "<td>director first name</td>";
					echo "<td>director last name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				
				while( $row = mysql_fetch_array( $result ) )
				{
					echo "ssssssss";
					$director_id = $row['director_id'];
					$director_first_name = $row['director_first_name'];
					$director_last_name = $row['director_last_name'];
					$count = $row['count'];
					
					$query = "select distinct director_id, item_id from Is_Produced_By where director_id='". $director_id. "'";
					$result2 = mysql_query( $query );
					$just = mysql_num_rows( $result2 );
					
					$query = "select distinct director_id, item_id, actor_id from Is_Produced_By where director_id='". $director_id. "'";
					$result2 = mysql_query( $query );
					$all = mysql_num_rows( $result2 );
					
					if( $all != 0 )
					{
						$count = $count * $just / $all;
					}
				
					echo "<tr>";
						echo "<td>";
							echo $director_id;
						echo "</td>";
						echo "<td>";
							echo $director_first_name;
						echo "</td>";
						echo "<td>";
							echo $director_last_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 10 )
		{
			$query = "select sum( borrow_count + reserve_count ) as count from Recorder natural join 
				Video natural join Item";
			$result = mysql_query( $query ) or die( mysql_error() );
			
			$query = "select recorder_id, recorder_first_name, recorder_last_name, sum( borrow_count + reserve_count ) as count from Recorder natural join 
				Video natural join Item 
				group by recorder_id having sum( borrow_count + reserve_count ) + 1 > 
				all (select sum( borrow_count + reserve_count ) from Recorder natural join Audio natural join Item group by recorder_id)";
			$result = mysql_query($query, $con) or die("error");
			
			echo "<table>";
				echo "<tr>";
					echo "<td>recorder id</td>";
					echo "<td>recorder first name</td>";
					echo "<td>recorder last name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$recorder_id = $row['recorder_id'];
					$recorder_first_name = $row['recorder_first_name'];
					$recorder_last_name = $row['recorder_last_name'];
					$count = $row['count'];
					echo "<tr>";
						echo "<td>";
							echo $recorder_id;
						echo "</td>";
						echo "<td>";
							echo $recorder_first_name;
						echo "</td>";
						echo "<td>";
							echo $recorder_last_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 11 )
		{
			$query = "select sum( borrow_count + reserve_count ) as count from Recorder natural join 
				Video natural join Item";
			$result = mysql_query( $query ) or die( mysql_error() );
			
			$query = "select recorder_id, recorder_first_name, recorder_last_name, sum( borrow_count + reserve_count ) as count from Recorder natural join 
				Audio natural join Item 
				group by recorder_id having sum( borrow_count + reserve_count ) + 1  > 
				all (select sum( borrow_count + reserve_count ) from Recorder natural join Audio natural join Item group by recorder_id)";
			$result = mysql_query($query, $con) or die("error");
			
			echo "<table>";
				echo "<tr>";
					echo "<td>recorder id</td>";
					echo "<td>recorder first name</td>";
					echo "<td>recorder last name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$recorder_id = $row['recorder_id'];
					$recorder_first_name = $row['recorder_first_name'];
					$recorder_last_name = $row['recorder_last_name'];
					$count = $row['count'];
					echo "<tr>";
						echo "<td>";
							echo $recorder_id;
						echo "</td>";
						echo "<td>";
							echo $recorder_first_name;
						echo "</td>";
						echo "<td>";
							echo $recorder_last_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		else if( $id == 12 )
		{
			//$query = "select * from recorder natural join audio natural join item";
			$query = "select policy_name, count(*) as count from Library_Hours_Policy natural join Managed_By group by policy_name having count(*) >=
				all ( select count(*) from Library_Hours_Policy natural join Managed_By group by policy_name )";
			$result = mysql_query($query, $con) or die("error");
			
			echo "<table>";
				echo "<tr>";
					echo "<td>policy name</td>";
					echo "<td>count</td>";
				echo "</tr>";
				while( $row = mysql_fetch_array( $result ) )
				{
					$policy_name = $row['policy_name'];
					$count = $row['count'];
					echo "<tr>";
						echo "<td>";
							echo $policy_name;
						echo "</td>";
						echo "<td>";
							echo $count;
						echo "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
	}
	
	echo '</body></html>';
?>
