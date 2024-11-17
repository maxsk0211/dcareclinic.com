<?php 
  session_start();
  // require '../chk-session.php';
  require '../../dbcon.php';


    $username = $_POST['users_username'];
    $password = $_POST['users_password']; // ควร hash รหัสผ่านก่อนบันทึกลงฐานข้อมูล
    $fname = $_POST['users_fname'];
    $lname = $_POST['users_lname'];
    $nickname = $_POST['users_nickname'];
    $tel = $_POST['users_tel'];
    $position_id = $_POST['position_id'];

    $license = $_POST['users_license'];
    $branch_id=$_POST['branch_id'];
    
        // ตรวจสอบว่า username ซ้ำหรือไม่
    $check_username_sql = "SELECT * FROM users WHERE users_username = '$username'";

    $result = $conn->query($check_username_sql);

    if ($result->num_rows > 0) {
        // username ซ้ำ
          $_SESSION['chk_users_username']=$username ;
          $_SESSION['chk_users_fname']=$fname;
          $_SESSION['chk_users_lname']=$lname;
          $_SESSION['chk_users_nickname']=$nickname;
          $_SESSION['chk_users_tel']=$tel;
          $_SESSION['chk_position_id']=$position_id;
          $_SESSION['chk_users_license']=$license;
          
          $_SESSION['msg_error']="ชื่อผู้ใช้งานระบบซ้ำ กรุณาใช้ชื่ออื่น!!";
          $_SESSION['insert_error']="1";
          header("location: ../users.php");
          exit();
    } else {
        // username ไม่ซ้ำ, ดำเนินการบันทึกข้อมูล
        $sql = "INSERT INTO users (users_username, users_password, users_fname, users_lname, users_nickname, users_tel, position_id, users_license,branch_id)
        VALUES ('$username', '$password', '$fname', '$lname', '$nickname', '$tel', '$position_id', '$license','$branch_id')";

        if ($conn->query($sql) === TRUE) {
            //echo "New record created successfully";
            $_SESSION['msg_ok']="เพิ่มผู้ใช้งานสำเสร็จ";
            header("location: ../users.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();

 ?>