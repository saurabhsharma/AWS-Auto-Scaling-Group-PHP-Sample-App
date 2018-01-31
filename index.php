<?php
	// Database Constants
	define("DB_SERVER", "oyecloud.cxpi1dg8gxhg.us-east-1.rds.amazonaws.com");
	define("DB_USER", "oyecloud");
	define("DB_PASS", "oyecloud");
	define("DB_NAME", "oyecloud");

	//All Errors
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	// 1. Create a database connection
	$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

	// Connection
	if (mysqli_connect_errno()) {
    		printf("Connect failed: %s\n", mysqli_connect_error());
    		exit();
	}

	// Base name
	if ($result = mysqli_query($connection, "SELECT DATABASE()")) {
    		$row = mysqli_fetch_row($result);
 		// printf("Default database is %s.\n", $row[0]);
    		mysqli_free_result($result);
	}

	// Create table if not exist
	if ($result = mysqli_query($connection, "CREATE TABLE IF NOT EXISTS todo(id int(11) primary key AUTO_INCREMENT, task text, completed boolean, visible boolean)")) {
 		// printf("table exist");
	}

	function setTask( $task ) {
		global $connection;
		$query = "INSERT INTO todo (task, completed, visible) VALUES (\"{$task}\", 0, 1)";
		$result = mysqli_query($connection, $query);
		//echo mysql_error();
	}

	function confirm_query( $result_set ) {
		if ( !$result_set ) {
			die("Database query failed: " . mysqli_error() );
		}
	}

	// Delete All Rows from table
	function deleteRows () {
		global $connection;
		$query = "DELETE FROM todo";
		$result = mysqli_query( $query, $connection );
		$query = "ALTER TABLE todo AUTO_INCREMENT = 1";
		$result = mysqli_query($connection, $query);
	}

	// Set task completion flag to 1 using Task Number
	function completedTask ( $taskNum ) {
		global $connection;
		$query = "UPDATE todo SET completed = 1 WHERE id={$taskNum}";
		$result = mysqli_query($connection, $query);
		/*if ( ) {
			echo "Change Success, " . mysql_affected_rows() . " rows affected.";
		} else {
			echo mysql_error();
		}*/
	}

	// Set task visibility to 0 using Task Number
	function hideTask( $taskNum ) {
		global $connection;
		$newText = "Something";
		$query = "UPDATE todo SET visible=0 WHERE id={$taskNum}";
		$result = mysqli_query($connection, $query);
	}

	// Displays All Visible Tasks
	function getAllTask() {
		global $connection;
		$query = "SELECT * FROM todo WHERE visible=1";
		$result = mysqli_query($connection, $query);
		
		while($list = mysqli_fetch_array($result, MYSQLI_NUM)){
			//echo print_r($list) . "<br/>";
			echo "Task #" . $list[0] . ": " . $list[1] . "<br />";
		}
	}

	// Displays All Hidden Tasks
	function getHiddenTask() {
		global $connection;
		$query = "SELECT * FROM todo WHERE visible=0";
		$result = mysqli_query($connection, $query);
		
		while ( $list = mysqli_fetch_array($result) ) {
			echo "Task #" . $list[0] . ": " . $list[1] . "<br />";
		}
	}

	// Check for task
	if ( isset( $_POST['taskName'] ) && $_POST['taskName'] !== "" ) {		
		$taskName = $_POST['taskName'];
		#echo $taskName;
		setTask( $taskName );
	}

	# Check for hide task number
	if ( is_numeric($_POST['num']) ) {
		$taskNum = $_POST['num'];
		hideTask( $taskNum );
	}

	echo "<form name=\"input\" action=\"index.php\" method=\"post\" >";
	echo "<input type=\"text\" name=\"taskName\" placeholder=\"enter task here\"/>";
	echo "<br/>";
	//echo "<label>Enter Task Number to Hide: ";
	echo "<input type=\"text\" name=\"num\" placeholder=\"enter task number to hide here\"/>";	
	echo "<input type=\"submit\" name=\"submit\" />";
	echo "</form>";
	echo "<br/><br/>";
	echo "To-Do List: " . "<br/>";
	echo getAllTask();
	echo "<br/>";
	echo "Hidden List: " . "<br/>";
	echo getHiddenTask();

	mysqli_close($connection);
?>