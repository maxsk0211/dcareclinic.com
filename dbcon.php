<?php 
	date_default_timezone_set("Asia/Bangkok");


	//dcareclinic.com
	// $conn=mysqli_connect("localhost","chanchal_dcareclinic","bwCuJxdEn87J7wSSHJ9J","chanchal_dcareclinic") or die("เกิดข้อผืดพลาด");

	//demo.dcareclinic.com
	$conn=mysqli_connect("localhost","chanchal_demo_dcareclinic","xdyQ3hXeytBGDApRXeu2","chanchal_demo_dcareclinic") or die("เกิดข้อผืดพลาด");
	
	mysqli_set_charset($conn, "utf8");
 ?>