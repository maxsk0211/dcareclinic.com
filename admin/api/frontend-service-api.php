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

        // เพิ่มการตรวจสอบค่า status ให้แน่ใจ
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        // มั่นใจว่ามีค่าเป็น 0 หรือ 1 เท่านั้น
        $status = ($status === 1) ? 1 : 0;
        
        $custom_description = !empty($_POST['custom_description']) ? $_POST['custom_description'] : null;
        $additional_features = !empty($_POST['additional_features']) ? $_POST['additional_features'] : null;
        
        // ตรวจสอบข้อมูล
        if (empty($id) || empty($frontend_category_id)) {
            $response = ['success' => false, 'message' => 'กรุณาระบุข้อมูลให้ถูกต้อง'];
            break;
        }
        
        // ทางเลือกที่ปลอดภัยที่สุด: ใช้การ execute query แบบง่าย
        // แต่ละส่วนได้รับการตรวจสอบแล้ว และใช้ prepared statements แบบแยกส่วน
        
        try {
            // เริ่มต้นด้วยการเตรียมข้อมูลพื้นฐาน
            $sql_base = "UPDATE frontend_services SET 
                    frontend_category_id = ?, 
                    display_order = ?, 
                    badge_text = ?, 
                    is_featured = ?, 
                    custom_price = ?, 
                    custom_original_price = ?, 
                    custom_description = ?, 
                    session_duration = ?, 
                    additional_features = ?, 
                    status = ?";
            
            // จัดการไฟล์รูปภาพ (ถ้ามี)
            $new_image_path = null;
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
                    $new_image_path = $new_filename;
                    
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
                    
                    // เพิ่ม SQL สำหรับอัพเดทรูปภาพ
                    $sql_base .= ", image_path = '" . $conn->real_escape_string($new_image_path) . "'";
                } else {
                    $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
                    break;
                }
            }
            
            // เพิ่ม WHERE clause
            $sql_base .= " WHERE id = ?";
            
            // prepare statement
            $stmt = $conn->prepare($sql_base);
            
            // ตรวจสอบความถูกต้องของ SQL
            if ($stmt === false) {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error];
                break;
            }
            
            // บันทึก SQL ที่ใช้ในการดีบัก
            error_log("SQL: " . $sql_base);
            
            // bind parameters - จำนวนและลำดับต้องตรงกัน
            $stmt->bind_param("iisiddsssii", 
                $frontend_category_id,     // i - Integer
                $display_order,            // i - Integer
                $badge_text,               // s - String
                $is_featured,              // i - Integer
                $custom_price,             // d - Double
                $custom_original_price,    // d - Double
                $custom_description,       // s - String
                $session_duration,         // s - String
                $additional_features,      // s - String
                $status,                   // i - Integer
                $id                        // i - Integer
            );
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'แก้ไขบริการเรียบร้อยแล้ว'];
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
            }
        } catch (Exception $e) {
            // จับข้อผิดพลาดทั้งหมด
            $response = [
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
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
                        <input class="form-check-input" type="radio" name="status" id="edit_status_active" value="1" '.($data['status'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_active">แสดง</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="edit_status_active" value="0" '.($data['status'] == 0 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_active">ซ่อน</label>
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

    // เพิ่มเคส quick_view ใน switch ของไฟล์ api/frontend-service-api.php
    case 'quick_view':
        // ดึงข้อมูลบริการแบบเร็ว
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("
            SELECT fs.*, c.course_name, c.course_price, c.course_pic, c.course_detail, fc.name as category_name 
            FROM frontend_services fs
            JOIN course c ON fs.course_id = c.course_id
            JOIN frontend_categories fc ON fs.frontend_category_id = fc.id
            WHERE fs.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // คำนวณราคาและราคาพิเศษ
            $price = $data['custom_price'] ?? $data['course_price'];
            $originalPrice = $data['custom_original_price'] ?? null;
            
            // กำหนดรูปภาพ
            $imgPath = $data['image_path'] ?? $data['course_pic'] ?? 'course.png';
            $imgUrl = "../../img/course/{$imgPath}";
            
            // สร้าง HTML ข้อมูลสำหรับแสดงใน Quick View
            $html = '
            <div class="row">
                <div class="col-md-5 text-center mb-3 mb-md-0">
                    <img src="'.$imgUrl.'" alt="'.$data['course_name'].'" class="img-fluid rounded">
                </div>
                <div class="col-md-7">
                    <h5>'.$data['course_name'].'</h5>
                    <span class="badge bg-primary">'.$data['category_name'].'</span>
                    '.($data['badge_text'] ? '<span class="badge bg-warning ms-1">'.$data['badge_text'].'</span>' : '').'
                    '.($data['is_featured'] ? '<span class="badge bg-success ms-1">บริการแนะนำ</span>' : '').'
                    
                    <div class="mt-3">
                        <h6>ราคา</h6>
                        <div class="d-flex align-items-center">
                            '.($originalPrice ? '<span class="text-decoration-line-through text-muted me-2">'.number_format($originalPrice, 0).' บาท</span>' : '').'
                            <span class="fs-5 fw-bold">'.number_format($price, 0).' บาท</span>
                        </div>
                    </div>
                    
                    '.($data['session_duration'] ? '
                    <div class="mt-3">
                        <h6>ระยะเวลาต่อครั้ง</h6>
                        <p class="mb-0">'.$data['session_duration'].' นาที</p>
                    </div>' : '').'
                    
                    <div class="mt-3">
                        <h6>รายละเอียด</h6>
                        <p class="mb-0">'.($data['custom_description'] ?? $data['course_detail'] ?? 'ไม่มีข้อมูล').'</p>
                    </div>
                    
                    '.($data['additional_features'] ? '
                    <div class="mt-3">
                        <h6>คุณสมบัติเพิ่มเติม</h6>
                        <ul class="ps-3 mb-0">
                            '.implode('', array_map(function($feature) {
                                return '<li>'.trim($feature).'</li>';
                            }, explode("\n", $data['additional_features']))).'
                        </ul>
                    </div>' : '').'
                    
                    <div class="mt-3">
                        <h6>การแสดงผล</h6>
                        <p class="mb-0">ลำดับการแสดงผล: '.$data['display_order'].'</p>
                        <p class="mb-0">สถานะ: '.($data['status'] == 1 ? '<span class="text-success">แสดง</span>' : '<span class="text-muted">ซ่อน</span>').'</p>
                    </div>
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

    case 'toggle_status':
        // เปลี่ยนสถานะการแสดงผล
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการเปลี่ยนสถานะ'];
            break;
        }
        
        $stmt = $conn->prepare("UPDATE frontend_services SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        
        if ($stmt->execute()) {
            $statusText = $status == 1 ? 'แสดง' : 'ซ่อน';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$statusText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        
        break;

    case 'toggle_featured':
        // เปลี่ยนสถานะบริการแนะนำ
        $id = $_POST['id'] ?? 0;
        $featured = $_POST['featured'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการเปลี่ยนสถานะ'];
            break;
        }
        
        $stmt = $conn->prepare("UPDATE frontend_services SET is_featured = ? WHERE id = ?");
        $stmt->bind_param("ii", $featured, $id);
        
        if ($stmt->execute()) {
            $featuredText = $featured == 1 ? 'บริการแนะนำ' : 'บริการทั่วไป';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$featuredText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        
        break;
    case 'get_course_details':
        // ดึงข้อมูลรายละเอียดคอร์ส
        $course_id = $_GET['course_id'] ?? 0;
        
        if (empty($course_id)) {
            $response = ['success' => false, 'message' => 'ไม่ระบุรหัสคอร์ส'];
            break;
        }
        
        $stmt = $conn->prepare("
            SELECT course_id, course_name, course_price, course_pic, course_detail 
            FROM course 
            WHERE course_id = ? AND course_status = 1
        ");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $response = [
                'success' => true, 
                'data' => $data
            ];
        } else {
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลคอร์ส'];
        }
        
        break;
}

echo json_encode($response);
exit;
?>