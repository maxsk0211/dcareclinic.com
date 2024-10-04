<?php
session_start(); 

include '../chk-session.php';
require '../../dbcon.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch_name = $_POST['branch_name'];

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ...

    // เตรียมคำสั่ง SQL INSERT
    $sql = "INSERT INTO branch (branch_name) VALUES (?)"; 
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // ผูกค่าพารามิเตอร์กับคำสั่ง SQL
        $stmt->bind_param("s", $branch_name);

        // ดำเนินการบันทึกข้อมูล
        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "เพิ่มข้อมูลสาขาเรียบร้อยแล้ว"; 
            
            // รับ branch_id สุดท้ายที่ถูกเพิ่ม
            $branch_id = $conn->insert_id;

            // เตรียมคำสั่ง SQL เพื่อเพิ่มเวลาทำการ
            $sql_day = "INSERT INTO clinic_hours (day_of_week, start_time, end_time, is_closed, branch_id) 
                        VALUES
                            ('Monday', '09:00:00', '17:00:00', 0, '$branch_id'),
                            ('Tuesday', '09:00:00', '17:00:00', 0, '$branch_id'),
                            ('Wednesday', '09:00:00', '17:00:00', 0, '$branch_id'),
                            ('Thursday', '09:00:00', '17:00:00', 0, '$branch_id'),
                            ('Friday', '09:00:00', '17:00:00', 0, '$branch_id'),
                            ('Saturday', '10:00:00', '17:00:00', 0, '$branch_id'),
                            ('Sunday', '09:00:00', '17:00:00', 0, '$branch_id');";

            // ดำเนินการบันทึกเวลาทำการ
            if ($conn->query($sql_day) === TRUE) {
                // $_SESSION['msg_ok'] .= " และเพิ่มเวลาทำการเรียบร้อยแล้ว"; 
            } else {
                $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกเวลาทำการ: " . $conn->error;
            }          
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }

        // ปิด statement
        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล (ถ้าจำเป็น)
// ...

// Redirect กลับไปยังหน้าเดิม หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../branch.php"); // หรือเปลี่ยนเป็นหน้าอื่นๆ ตามต้องการ
exit(); 
?>
