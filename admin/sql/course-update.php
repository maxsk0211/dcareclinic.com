<?php
session_start();
require '../../dbcon.php';

// เพิ่มฟังก์ชันสำหรับฟอร์แมตรหัส
function formatCourseId($id) {
    return 'C-' . str_pad($id, 6, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // เริ่ม transaction
        $conn->begin_transaction();

        // เก็บข้อมูลเดิมก่อนอัพเดท
        $sql_old_data = "SELECT * FROM course WHERE course_id = ?";
        $stmt_old = $conn->prepare($sql_old_data);
        $stmt_old->bind_param('i', $_POST['course_id']);
        $stmt_old->execute();
        $old_data = $stmt_old->get_result()->fetch_assoc();
        $stmt_old->close();

        // รับค่าและทำความสะอาดข้อมูล
        $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
        $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
        $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
        $course_detail = mysqli_real_escape_string($conn, $_POST['course_detail']);
        $course_price = mysqli_real_escape_string($conn, $_POST['course_price']);
        $course_amount = mysqli_real_escape_string($conn, $_POST['course_amount']);
        $course_type_id = mysqli_real_escape_string($conn, $_POST['course_type_id']);
        $course_start = mysqli_real_escape_string($conn, $_POST['course_start']);
        $course_end = mysqli_real_escape_string($conn, $_POST['course_end']);
        $course_note = mysqli_real_escape_string($conn, $_POST['course_note']);
        $course_status = mysqli_real_escape_string($conn, $_POST['course_status']);

        // อัปโหลดรูปภาพ (ถ้ามีการอัปโหลดรูปภาพใหม่)
        if (isset($_FILES['course_pic']) && $_FILES['course_pic']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../../img/course/";
            $originalFileName = basename($_FILES["course_pic"]["name"]);
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

            // สร้างชื่อไฟล์แบบสุ่ม
            $randomFileName = uniqid() . '.' . $fileExtension;
            $targetFilePath = $targetDir . $randomFileName;

            $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($fileExtension, $allowTypes)) {
                if (move_uploaded_file($_FILES["course_pic"]["tmp_name"], $targetFilePath)) {
                    $course_pic = $randomFileName;

                    // ลบรูปภาพเก่า (ถ้ามี)
                    $sql_get_old_image = "SELECT course_pic FROM course WHERE course_id = '$course_id'";
                    $result_get_old_image = mysqli_query($conn, $sql_get_old_image);
                    if ($result_get_old_image && $row_get_old_image = mysqli_fetch_object($result_get_old_image)) {
                        $old_image_path = "../../img/course/" . $row_get_old_image->course_pic;
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                } else {
                    $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
                    header("Location: ../course.php");
                    exit();
                }
            } else {
                $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
                header("Location: ../course.php");
                exit();
            }
        } else {
            // ถ้าไม่มีการอัปโหลดรูปภาพใหม่ ให้ใช้รูปภาพเดิม
            $sql_get_image = "SELECT course_pic FROM course WHERE course_id = '$course_id'";
            $result_get_image = mysqli_query($conn, $sql_get_image);
            $row_get_image = mysqli_fetch_object($result_get_image);
            $course_pic = $row_get_image->course_pic;
        }

        // แปลงวันที่
        $thai_start_date = DateTime::createFromFormat('d/m/Y', $course_start);
        $thai_end_date = DateTime::createFromFormat('d/m/Y', $course_end);
        if ($thai_start_date && $thai_end_date) {
            $thai_start_date->modify('-543 year');
            $thai_end_date->modify('-543 year');
            $course_start = $thai_start_date->format('Y-m-d');
            $course_end = $thai_end_date->format('Y-m-d');
        } else {
            throw new Exception("รูปแบบวันที่ไม่ถูกต้อง");
        }

        // อัพเดทข้อมูล
        $update_sql = "UPDATE course SET 
            branch_id = ?, course_name = ?, course_detail = ?,
            course_price = ?, course_amount = ?, course_type_id = ?,
            course_start = ?, course_end = ?, course_pic = ?,
            course_note = ?, course_status = ?
            WHERE course_id = ?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('issiiiisssis',
            $branch_id, $course_name, $course_detail,
            $course_price, $course_amount, $course_type_id,
            $course_start, $course_end, $course_pic,
            $course_note, $course_status, $course_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating course: " . $stmt->error);
        }

        // สร้าง details สำหรับ log
        $changes = [];
        foreach ($old_data as $key => $value) {
            $new_value = $_POST[$key] ?? null;
            if ($value != $new_value && $key != 'course_pic') {
                $changes[$key] = [
                    'from' => $value,
                    'to' => $new_value
                ];
            }
        }

        $formatted_id = formatCourseId($course_id);
        $details = json_encode([
            'changes' => $changes,
            'course_name' => $course_name,
            'course_code' => $formatted_id
        ], JSON_UNESCAPED_UNICODE);

        // บันทึก log
        $log_sql = "INSERT INTO activity_logs 
                    (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'update', 'course', ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param('iisi', 
            $_SESSION['users_id'],
            $course_id,
            $details,
            $_SESSION['branch_id']
        );
        $log_stmt->execute();

        // Commit transaction
        $conn->commit();
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลคอร์สเรียบร้อยแล้ว";

    } catch (Exception $e) {
        // Rollback transaction ถ้าเกิดข้อผิดพลาด
        $conn->rollback();
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

header("Location: ../course.php");
exit();
?>