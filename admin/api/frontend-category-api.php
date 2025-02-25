<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

header('Content-Type: application/json');

// ตรวจสอบการเรียกใช้งาน API
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'ไม่ระบุการกระทำ (action)'];

switch ($action) {
    case 'add':
        // เพิ่มหมวดหมู่ใหม่
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $course_type_id = !empty($_POST['course_type_id']) ? $_POST['course_type_id'] : null;
        $display_order = $_POST['display_order'] ?? 0;
        $status = $_POST['status'] ?? 1;
        
        // ตรวจสอบข้อมูล
        if (empty($name) || empty($slug)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'];
            break;
        }
        
        // ตรวจสอบ slug ซ้ำ
        $stmt = $conn->prepare("SELECT id FROM frontend_categories WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Slug นี้ถูกใช้งานแล้ว กรุณาใช้ Slug อื่น'];
            break;
        }
        
        // ตรวจสอบ course_type_id
        if (!empty($course_type_id)) {
            $stmt = $conn->prepare("SELECT course_type_id FROM course_type WHERE course_type_id = ?");
            $stmt->bind_param("i", $course_type_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $response = ['success' => false, 'message' => 'ประเภทคอร์สที่เลือกไม่ถูกต้อง'];
                break;
            }
        }
        
        // บันทึกข้อมูล
        $stmt = $conn->prepare("INSERT INTO frontend_categories (name, slug, course_type_id, display_order, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $name, $slug, $course_type_id, $display_order, $status);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'เพิ่มหมวดหมู่เรียบร้อยแล้ว', 'id' => $stmt->insert_id];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'update':
        // แก้ไขหมวดหมู่
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $course_type_id = !empty($_POST['course_type_id']) ? $_POST['course_type_id'] : null;
        $display_order = $_POST['display_order'] ?? 0;
        $status = $_POST['status'] ?? 1;
        
        // ตรวจสอบข้อมูล
        if (empty($id) || empty($name) || empty($slug)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'];
            break;
        }
        
        // ตรวจสอบ slug ซ้ำ (ยกเว้น ID ปัจจุบัน)
        $stmt = $conn->prepare("SELECT id FROM frontend_categories WHERE slug = ? AND id != ?");
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Slug นี้ถูกใช้งานแล้ว กรุณาใช้ Slug อื่น'];
            break;
        }
        
        // ตรวจสอบ course_type_id
        if (!empty($course_type_id)) {
            $stmt = $conn->prepare("SELECT course_type_id FROM course_type WHERE course_type_id = ?");
            $stmt->bind_param("i", $course_type_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $response = ['success' => false, 'message' => 'ประเภทคอร์สที่เลือกไม่ถูกต้อง'];
                break;
            }
        }
        
        // อัพเดทข้อมูล
        $stmt = $conn->prepare("UPDATE frontend_categories SET name = ?, slug = ?, course_type_id = ?, display_order = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssiiii", $name, $slug, $course_type_id, $display_order, $status, $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'แก้ไขหมวดหมู่เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'delete':
        // ลบหมวดหมู่
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ'];
            break;
        }
        
        // ตรวจสอบว่ามีบริการที่ใช้หมวดหมู่นี้หรือไม่
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM frontend_services WHERE frontend_category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $response = ['success' => false, 'message' => 'ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากมีบริการที่ใช้หมวดหมู่นี้อยู่ กรุณาลบหรือย้ายบริการออกก่อน'];
            break;
        }
        
        // ลบข้อมูล
        $stmt = $conn->prepare("DELETE FROM frontend_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'ลบหมวดหมู่เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'get':
        // ดึงข้อมูลหมวดหมู่
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("SELECT * FROM frontend_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $response = ['success' => true, 'data' => $data];
        } else {
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลหมวดหมู่'];
        }
        
        break;
}

echo json_encode($response);
exit;
?>