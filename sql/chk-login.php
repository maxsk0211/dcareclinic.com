<?php 
	session_start();
	// if (isset($_SESSION['users_id'])) {
	// 	header("location: ../login.php");
	// 	exit();
	// }
	require '../dbcon.php';

	$username=$_POST['users_username'];
	$password=$_POST['users_password'];


	$sql="select * from users,employee where users_username='$username' and users_password= '$password'and users.emp_id=employee.emp_id  and users_status='1'";
	$result=mysqli_query($conn, $sql);
	
	if ($result) {
		$row_users=mysqli_fetch_object($result);
		if ($row_users->users_username==$username and $row_users->users_password==$password) {
			$_SESSION['users_id']=$row_users->users_id;
			//$_SESSION['users_fname']=$row->users_fname;
			//$_SESSION['users_lname']=$row->users_lname;
			$_SESSION['users_username']=$row_users->users_username;
			//$_SESSION['users_level']=$row->users_level;
			

			if ($row_users->position_id==1 or $row_users->position_id==2) {
					header("location: ../admin/index.php");
					exit();
			}elseif($row->users_level==3){
					header("location: ../users/index.php");
					exit();
			}
			

		}else{
		$_SESSION['login_error']="1";
		//header("location: ../login.php");
		exit();
		}
		
	}


 ?>