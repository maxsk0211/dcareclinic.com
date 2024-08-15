<?php 
	session_start();
	// if (isset($_SESSION['users_id'])) {
	// 	header("location: ../login.php");
	// 	exit();
	// }
	require '../dbcon.php';

	$username=$_POST['users_username'];
	$password=$_POST['users_password'];


	$sql="select * from users,position where users_username='$username' and users_password= '$password' and users.position_id=position.position_id";
	$result=mysqli_query($conn, $sql);
	
	if ($result) {
		$row_users=mysqli_fetch_object($result);
		if ($row_users->users_username==$username and $row_users->users_password==$password) {
			$_SESSION['users_id']=$row_users->users_id;
			$_SESSION['users_fname']=$row_users->users_fname;
			$_SESSION['users_lname']=$row_users->users_lname;
			$_SESSION['users_username']=$row_users->users_username;
			$_SESSION['position_id']=$row_users->position_id;
			$_SESSION['position_name']=$row_users->position_name;
			if ($_SESSION['position_id']!=1) {
				$_SESSION['branch_id']=$row_users->branch_id;
			}
			

			if ($row_users->position_id==1 or $row_users->position_id==2) {
					// admin - ผู้จัดการ
					header("location: ../admin/index.php");
					exit();
			}elseif($row_users->position_id==3){
					header("location: ../admin/index.php");
					exit();
			}elseif($row_users->users_status==0){
					$_SESSION['login_error']="ผู้ใข้งานถูกปิด กรุณาติดต่อผู้จัดการ";
					header("location: ../login.php");
					exit();
			}
			
		}else{
		$_SESSION['login_error']="ชื่อผู้ใช้หรือรหัสผ่านของคุณไม่ถูกต้อง";
		$_SESSION['users_username']=$_POST['users_username'];
		header("location: ../login.php");
		exit();
		}
		
	}
 ?>