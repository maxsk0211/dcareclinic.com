<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // ตรวจสอบข้อมูลที่ส่งมา
        if (!isset($_POST['course_id'], $_POST['password'], $_POST['reason'])) {
            throw new Exception('ข้อมูลไม่ครบถ้วน');
        }

        $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $user_id = $_SESSION['users_id'];

        // ตรวจสอบรหัสผ่าน
        $password_check_sql = "SELECT * FROM users WHERE users_id = '$user_id' AND users_password = '$password'";
        $password_result = mysqli_query($conn, $password_check_sql);

        if (mysqli_num_rows($password_result) == 0) {
            throw new Exception('รหัสผ่านไม่ถูกต้อง');
        }

        // เริ่ม transaction
        mysqli_begin_transaction($conn);

        // ดึงข้อมูลคอร์สก่อนลบ
        $course_sql = "SELECT * FROM course WHERE course_id = '$course_id'";
        $course_result = mysqli_query($conn, $course_sql);
        $course_data = mysqli_fetch_assoc($course_result);

        if (!$course_data) {
            throw new Exception('ไม่พบข้อมูลคอร์สที่ต้องการลบ');
        }

        // ลบรูปภาพ (ถ้ามี)
        if (!empty($course_data['course_pic']) && $course_data['course_pic'] != 'course.png') {
            $image_path = "../../img/course/" . $course_data['course_pic'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // ลบข้อมูลคอร์ส
        $delete_sql = "DELETE FROM course WHERE course_id = '$course_id'";
        if (!mysqli_query($conn, $delete_sql)) {
            throw new Exception("Error deleting course: " . mysqli_error($conn));
        }

        // บันทึก log
        $details = json_encode([
            'reason' => $reason,
            'deleted_data' => [
                'course_id' => $course_id,
                'course_name' => $course_data['course_name'],
                'course_price' => $course_data['course_price'],
                'course_amount' => $course_data['course_amount'],
                'course_detail' => $course_data['course_detail']
            ]
        ], JSON_UNESCAPED_UNICODE);

        $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                   VALUES ('$user_id', 'delete', 'course', '$course_id', '$details', '{$_SESSION['branch_id']}')";
        
        if (!mysqli_query($conn, $log_sql)) {
            throw new Exception("Error logging delete action: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        if (isset($conn)) {
            mysqli_rollback($conn);
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>