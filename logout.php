<?php 
	session_start();

	unset($_SESSION['line_user_id']);
	unset($_SESSION['line_access_token']);
	
	session_destroy();
	header("location: login.php");
 ?>