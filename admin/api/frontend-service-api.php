<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

header('Content-Type: application/json');

// ตรวจสอบการเรียกใช้งาน API
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'ไม่ระบุการกระทำ (action)'];

switch ($action) {
    case 'add':
        // เพิ่มบริการใหม่
        $course_id = $_POST['course_id'] ?? '';
        $frontend_category_id = $_POST['frontend_category_id'] ?? '';
        $custom_price = !empty($_POST['custom_price']) ? $_POST['custom_price'] : null;
        $custom_original_price = !empty($_POST['custom_original_price']) ? $_POST['custom_original_price'] : null;
        $badge_text = $_POST['badge_text'] ?? null;
        $session_duration = !empty($_POST['session_duration']) ? $_POST['session_duration'] : null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'] ?? 1;
        $custom_description = !empty($_POST['custom_description']) ? $_POST['custom_description'] : null;
        $additional_features = !empty($_POST['additional_features']) ? $_POST['additional_features'] : null;
        
        // ตรวจสอบข้อมูล
        if (empty($course_id) || empty($frontend_category_id)) {
            $response = ['success' => false, 'message' => 'กรุณาเลือกคอร์สและหมวดหมู่'];
            break;
        }
        
        // ตรวจสอบว่าบริการนี้มีในหมวดหมู่นี้แล้วหรือไม่
        $stmt = $conn->prepare("SELECT id FROM frontend_services WHERE course_id = ? AND frontend_category_id = ?");
        $stmt->bind_param("ii", $course_id, $frontend_category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'บริการนี้มีในหมวดหมู่นี้แล้ว กรุณาเลือกคอร์สหรือหมวดหมู่อื่น'];
            break;
        }
        
        // จัดการไฟล์รูปภาพ (ถ้ามี)
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/course/";
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $new_filename;
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
                break;
            }
        }
        
        // บันทึกข้อมูล
        $stmt = $conn->prepare("INSERT INTO frontend_services (course_id, frontend_category_id, display_order, badge_text, is_featured, custom_price, custom_original_price, custom_description, session_duration, additional_features, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisiddssssi", $course_id, $frontend_category_id, $display_order, $badge_text, $is_featured, $custom_price, $custom_original_price, $custom_description, $session_duration, $additional_features, $image_path, $status);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'เพิ่มบริการเรียบร้อยแล้ว', 'id' => $stmt->insert_id];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'update':
        // แก้ไขบริการ
        $id = $_POST['id'] ?? 0;
        $frontend_category_id = $_POST['frontend_category_id'] ?? '';
        $custom_price = !empty($_POST['custom_price']) ? $_POST['custom_price'] : null;
        $custom_original_price = !empty($_POST['custom_original_price']) ? $_POST['custom_original_price'] : null;
        $badge_text = $_POST['badge_text'] ?? null;
        $session_duration = !empty($_POST['session_duration']) ? $_POST['session_duration'] : null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'] ?? 1;
        $custom_description = !empty($_POST['custom_description']) ? $_POST['custom_description'] : null;
        $additional_features = !empty($_POST['additional_features']) ? $_POST['additional_features'] : null;
        
        // ตรวจสอบข้อมูล
        if (empty($id) || empty($frontend_category_id)) {
            $response = ['success' => false, 'message' => 'กรุณาระบุข้อมูลให้ถูกต้อง'];
            break;
        }
        
        // จัดการไฟล์รูปภาพ (ถ้ามี)
        $image_sql = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/course/";
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_sql = ", image_path = '" . $new_filename . "'";
                
                // ลบรูปเก่า (ถ้ามี)
                $stmt = $conn->prepare("SELECT image_path FROM frontend_services WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (!empty($row['image_path']) && file_exists($target_dir . $row['image_path'])) {
                        unlink($target_dir . $row['image_path']);
                    }
                }
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
                break;
            }
        }
        
        // อัพเดทข้อมูล
        $sql = "UPDATE frontend_services SET 
                frontend_category_id = ?, 
                display_order = ?, 
                badge_text = ?, 
                is_featured = ?, 
                custom_price = ?, 
                custom_original_price = ?, 
                custom_description = ?, 
                session_duration = ?, 
                additional_features = ?, 
                status = ? $image_sql 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisiddsssi", $frontend_category_id, $display_order, $badge_text, $is_featured, $custom_price, $custom_original_price, $custom_description, $session_duration, $additional_features, $status, $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'แก้ไขบริการเรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'delete':
        // ลบบริการ
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ'];
            break;
        }
        
        // ลบรูปภาพ (ถ้ามี)
        $stmt = $conn->prepare("SELECT image_path FROM frontend_services WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['image_path'])) {
                $image_path = "../../img/course/" . $row['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // ลบข้อมูล
        $stmt = $conn->prepare("DELETE FROM frontend_services WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'ลบบริการเรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'get':
        // ดึงข้อมูลบริการ
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("
            SELECT fs.*, c.course_name, c.course_price, c.course_pic, c.course_detail
            FROM frontend_services fs
            JOIN course c ON fs.course_id = c.course_id
            WHERE fs.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // สร้าง HTML สำหรับฟอร์มแก้ไข
            $html = '
            <input type="hidden" name="id" value="'.$data['id'].'">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">คอร์ส</label>
                    <input type="text" class="form-control" value="'.$data['course_name'].'" readonly>
                    <input type="hidden" name="course_id" value="'.$data['course_id'].'">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">หมวดหมู่</label>
                    <select class="form-select" name="frontend_category_id" required>
                        <option value="">-- กรุณาเลือกหมวดหมู่ --</option>';
                        
            // ดึงข้อมูลหมวดหมู่
            $cat_stmt = $conn->prepare("SELECT id, name FROM frontend_categories WHERE status = 1 ORDER BY display_order, name");
            $cat_stmt->execute();
            $cat_result = $cat_stmt->get_result();
            
            while ($cat_row = $cat_result->fetch_assoc()) {
                $selected = ($cat_row['id'] == $data['frontend_category_id']) ? 'selected' : '';
                $html .= '<option value="'.$cat_row['id'].'" '.$selected.'>'.$cat_row['name'].'</option>';
            }
                        
            $html .= '
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาพิเศษ (บาท)</label>
                    <input type="number" class="form-control" name="custom_price" step="0.01" value="'.$data['custom_price'].'" placeholder="ราคาจากคอร์ส: '.number_format($data['course_price'], 2).' บาท">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาปกติสำหรับแสดงส่วนลด (บาท)</label>
                    <input type="number" class="form-control" name="custom_original_price" step="0.01" value="'.$data['custom_original_price'].'" placeholder="ถ้าต้องการแสดงส่วนลด">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ข้อความป้ายกำกับ</label>
                    <input type="text" class="form-control" name="badge_text" value="'.$data['badge_text'].'" placeholder="เช่น ขายดี, แนะนำ">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ระยะเวลาต่อครั้ง (นาที)</label>
                    <input type="number" class="form-control" name="session_duration" value="'.$data['session_duration'].'" placeholder="เช่น 60, 90">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ลำดับการแสดงผล</label>
                    <input type="number" class="form-control" name="display_order" value="'.$data['display_order'].'" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label d-block">สถานะและตัวเลือก</label>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="edit_is_featured" value="1" '.($data['is_featured'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_is_featured">แสดงในส่วน Featured</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="edit_status_show" value="1" '.($data['status'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_show">แสดง</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="edit_status_hide" value="0" '.($data['status'] == 0 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_hide">ซ่อน</label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">คำอธิบายพิเศษ</label>
                    <textarea class="form-control" name="custom_description" rows="3" placeholder="ถ้าว่างไว้จะใช้คำอธิบายจากคอร์ส">'.$data['custom_description'].'</textarea>
                    <small class="text-muted">คำอธิบายจากคอร์ส: '.substr($data['course_detail'], 0, 100).(strlen($data['course_detail']) > 100 ? '...' : '').'</small>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">คุณสมบัติพิเศษเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                    <textarea class="form-control" name="additional_features" rows="3" placeholder="เช่น Deep Cleansing&#10;Gentle Exfoliation&#10;Face Massage">'.$data['additional_features'].'</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">รูปภาพ (ถ้าต้องการเปลี่ยน)</label>
                    <input type="file" class="form-control" name="image" id="edit_service_image" accept="image/*">
                    <small class="text-muted">ถ้าไม่อัปโหลดจะใช้รูปเดิม</small>
                    <div id="edit_image_preview"></div>
                </div>
            </div>
            ';
            
            $response = [
                'success' => true, 
                'data' => $data,
                'html' => $html
            ];
        } else {
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลบริการ'];
        }
        
        break;
}

echo json_encode($response);
exit;
?>