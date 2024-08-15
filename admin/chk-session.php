<?php 
	session_start();
	if (!isset($_SESSION['users_id'])) {
		header("location: ../logout.php");
	}
 ?>