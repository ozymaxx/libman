<?php
function connectToSql() {
	$conn=mysql_connect("localhost", "root", "");
	$sql="CREATE DATABASE if not exists cs353";
	  
	$query = "create database if not exists cs353";
	mysql_query( $query, $conn );
	
	mysql_select_db('cs353', $conn);
	
	$query = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"';
	mysql_query( $query, $conn );
	
	$query = 'SET time_zone = "+00:00"';
	mysql_query( $query, $conn );
	
	//
	// function here
	////////////root'u değiştir
	$query = "DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `LATE_PENALTY`(`endDate` DATETIME) RETURNS int(11)
    NO SQL
RETURN DATEDIFF( endDate, NOW()) * 5$$

DELIMITER";
//////////////////////////////////////
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Actor` (
  `actor_id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_first_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `actor_last_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`actor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Audio` (
  `item_id` int(11) NOT NULL,
  `duration` time NOT NULL,
  `recorder_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `recorder_id` (`recorder_id`),
  KEY `duration` (`duration`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Author` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_first_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `author_last_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Book` (
  `item_id` int(11) NOT NULL,
  `publisher` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `page_count` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `page_count` (`page_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Director` (
  `director_id` int(11) NOT NULL AUTO_INCREMENT,
  `director_first_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `director_last_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`director_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Directs` (
  `director_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  PRIMARY KEY (`director_id`,`actor_id`),
  KEY `Directs_ibfk_2` (`actor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Is_Produced_By` (
  `actor_id` int(11) NOT NULL,
  `director_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`actor_id`,`director_id`,`item_id`),
  KEY `item_id` (`item_id`),
  KEY `Is_Produced_By_ibfk_2` (`director_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Is_Written_By` (
  `author_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  PRIMARY KEY (`author_id`,`book_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `picture_addr` text COLLATE utf8_turkish_ci NOT NULL,
  `borrow_count` int(11) NOT NULL,
  `reserve_count` int(11) NOT NULL,
  `publication_date` year(4) NOT NULL,
  `shelf_id` int(11) NOT NULL,
  `borrowed_visitor_id` int(11) DEFAULT NULL,
  `reserved_visitor_id` int(11) DEFAULT NULL,
  `borrow_start_time` datetime DEFAULT NULL,
  `borrow_end_time` datetime DEFAULT NULL,
  `reserve_start_time` datetime DEFAULT NULL,
  `reserve_end_supposed_time` datetime DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `shelf_id` (`shelf_id`),
  KEY `borrowed_visitor_id` (`borrowed_visitor_id`),
  KEY `reserved_visitor_id` (`reserved_visitor_id`),
  KEY `publication_date` (`publication_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Library` (
  `library_id` int(11) NOT NULL AUTO_INCREMENT,
  `library_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `library_addr` text COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=2";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Library_Hours_Policy` (
  `policy_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `policy_validity_start` datetime NOT NULL,
  `policy_validity_end` datetime NOT NULL,
  PRIMARY KEY (`policy_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Managed_By` (
  `library_id` int(11) NOT NULL,
  `policy_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`library_id`,`policy_name`),
  KEY `policy_name` (`policy_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Recorder` (
  `recorder_id` int(11) NOT NULL AUTO_INCREMENT,
  `recorder_first_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `recorder_last_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`recorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Room` (
  `room_no` int(11) NOT NULL,
  `library_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `room_type` enum('media','study') COLLATE utf8_turkish_ci NOT NULL,
  `visit_count` int(11) NOT NULL,
  PRIMARY KEY (`room_no`,`library_id`),
  KEY `Room_ibfk_1` (`library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Section` (
  `section_no` int(11) NOT NULL,
  `library_id` int(11) NOT NULL,
  `section_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`section_no`,`library_id`),
  KEY `library_id` (`library_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Shelf` (
  `shelf_id` int(11) NOT NULL AUTO_INCREMENT,
  `shelf_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `capacity` int(11) NOT NULL,
  `shelf_type` enum('audio','video','book') COLLATE utf8_turkish_ci NOT NULL,
  `section_no` int(11) NOT NULL,
  `library_id` int(11) NOT NULL,
  PRIMARY KEY (`shelf_id`),
  KEY `section_no` (`section_no`),
  KEY `library_id` (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=2";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Video` (
  `item_id` int(11) NOT NULL,
  `duration` time NOT NULL,
  `recorder_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `recorder_id` (`recorder_id`),
  KEY `duration` (`duration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Visitor` (
  `visitor_id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_pwd` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `visitor_first_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `visitor_last_name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `visitor_room_no` int(11) DEFAULT NULL,
  `visitor_room_libid` int(11) DEFAULT NULL,
  `visitor_room_start_time` datetime DEFAULT NULL,
  `visitor_room_end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`visitor_id`),
  KEY `visitor_room_no` (`visitor_room_no`),
  KEY `visitor_room_libid` (`visitor_room_libid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1";
	mysql_query( $query, $conn );
	
	$query = "CREATE TABLE IF NOT EXISTS `Visits` (
  `library_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `auth_level` enum('admin','only') CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`library_id`,`visitor_id`),
  KEY `Visits_ibfk_2` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Audio`
  ADD CONSTRAINT `Audio_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `Item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Audio_ibfk_2` FOREIGN KEY (`recorder_id`) REFERENCES `Recorder` (`recorder_id`) ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Book`
  ADD CONSTRAINT `Book_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `Item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Directs`
  ADD CONSTRAINT `Directs_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `Actor` (`actor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Directs_ibfk_1` FOREIGN KEY (`director_id`) REFERENCES `Director` (`director_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Is_Produced_By`
  ADD CONSTRAINT `Is_Produced_By_ibfk_2` FOREIGN KEY (`director_id`) REFERENCES `Director` (`director_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Is_Produced_By_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `Actor` (`actor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Is_Produced_By_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `Video` (`item_id`) ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Is_Written_By`
  ADD CONSTRAINT `Is_Written_By_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `Book` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Is_Written_By_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `Author` (`author_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Item`
  ADD CONSTRAINT `Item_ibfk_1` FOREIGN KEY (`borrowed_visitor_id`) REFERENCES `Visitor` (`visitor_id`),
  ADD CONSTRAINT `Item_ibfk_2` FOREIGN KEY (`reserved_visitor_id`) REFERENCES `Visitor` (`visitor_id`),
  ADD CONSTRAINT `Item_ibfk_3` FOREIGN KEY (`shelf_id`) REFERENCES `Shelf` (`shelf_id`) ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Managed_By`
  ADD CONSTRAINT `Managed_By_ibfk_1` FOREIGN KEY (`library_id`) REFERENCES `Library` (`library_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Managed_By_ibfk_2` FOREIGN KEY (`policy_name`) REFERENCES `Library_Hours_Policy` (`policy_name`)";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Room`
  ADD CONSTRAINT `Room_ibfk_1` FOREIGN KEY (`library_id`) REFERENCES `Library` (`library_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Section`
  ADD CONSTRAINT `Section_ibfk_1` FOREIGN KEY (`library_id`) REFERENCES `Library` (`library_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Shelf`
  ADD CONSTRAINT `Shelf_ibfk_1` FOREIGN KEY (`section_no`) REFERENCES `Section` (`section_no`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Shelf_ibfk_2` FOREIGN KEY (`library_id`) REFERENCES `Library` (`library_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Video`
  ADD CONSTRAINT `Video_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `Item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Video_ibfk_2` FOREIGN KEY (`recorder_id`) REFERENCES `Recorder` (`recorder_id`) ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Visitor`
  ADD CONSTRAINT `Visitor_ibfk_1` FOREIGN KEY (`visitor_room_no`) REFERENCES `Room` (`room_no`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Visitor_ibfk_2` FOREIGN KEY (`visitor_room_libid`) REFERENCES `Library` (`library_id`) ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "ALTER TABLE `Visits`
  ADD CONSTRAINT `Visits_ibfk_1` FOREIGN KEY (`library_id`) REFERENCES `Library` (`library_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Visits_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `Visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE";
	mysql_query( $query, $conn );
	
	$query = "DELIMITER $$
CREATE EVENT `AUTOEND_RESERVATION` ON SCHEDULE EVERY 1 DAY STARTS '2013-12-31 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE Item SET reserved_visitor_id = NULL, reserve_start_time = NULL, reserve_end_supposed_time = NULL WHERE NOW() > reserve_end_supposed_time$$
DELIMITER";
	mysql_query( $query, $conn );
	
	$libraries = mysql_query( 'SELECT * FROM Library');
	$visitors = mysql_query( 'SELECT * FROM Visitor');
	
	echo "out";
	
	if ( mysql_num_rows( $visitors ) == 0 && mysql_num_rows( $libraries ) == 0 )
	{
		echo "enter";
		//$lib_name = "root library";
		$query = "Insert into Library (library_name, library_addr) values ('root libary', 'bilkent üniversitesi')";
		mysql_query( $query, $conn ) or die( mysql_error() );
		
		$result = mysql_query("SHOW TABLE STATUS LIKE 'Library'", $conn);
		$data = mysql_fetch_assoc($result) or die("error");
		$library_id = $data['Auto_increment'];
		$library_id = $library_id - 1;
		
		$query = "Insert into Visitor (visitor_pwd, visitor_first_name, visitor_last_name) values ('root', 'root name', 'root last name')";
		mysql_query( $query, $conn ) or die( mysql_error() );
		
		$result = mysql_query("SHOW TABLE STATUS LIKE 'Visitor'", $conn);
		$data = mysql_fetch_assoc($result) or die("error");
		$visitor_id = $data['Auto_increment'];
		$visitor_id = $visitor_id - 1;
		
		$query = "Insert into Visits (library_id, visitor_id, auth_level) values ('$library_id', '$visitor_id', 'admin')";
		mysql_query( $query, $conn ) or die( mysql_error() );
	}
	
	mysql_query('SET NAMES UTF8;',$conn);
	return $conn;
}
?>

