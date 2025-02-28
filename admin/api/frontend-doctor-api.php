<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

// ตรวจสอบการเรียกใช้งาน API
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'ไม่ระบุการกระทำ (action)'];

switch ($action) {
    case 'add':
        // เพิ่มแพทย์ใหม่
        $title = $_POST['title'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $nickname = $_POST['nickname'] ?? null;
        $specialty = $_POST['specialty'] ?? '';
        $education = $_POST['education'] ?? null;
        $certification = $_POST['certification'] ?? null;
        $achievements = $_POST['achievements'] ?? null;
        $additional_features = $_POST['additional_features'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        
        // ตรวจสอบข้อมูล
        if (empty($title) || empty($first_name) || empty($last_name) || empty($specialty)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน'];
            break;
        }
        
        // จัดการไฟล์รูปภาพ (ถ้ามี)
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/doctors/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
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
        $stmt = $conn->prepare("INSERT INTO frontend_doctors (title, first_name, last_name, nickname, specialty, education, certification, achievements, additional_features, image_path, display_order, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssiii", $title, $first_name, $last_name, $nickname, $specialty, $education, $certification, $achievements, $additional_features, $image_path, $display_order, $is_featured, $status);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'เพิ่มข้อมูลแพทย์เรียบร้อยแล้ว', 'id' => $stmt->insert_id];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'update':
        // แก้ไขข้อมูลแพทย์
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $nickname = $_POST['nickname'] ?? null;
        $specialty = $_POST['specialty'] ?? '';
        $education = $_POST['education'] ?? null;
        $certification = $_POST['certification'] ?? null;
        $achievements = $_POST['achievements'] ?? null;
        $additional_features = $_POST['additional_features'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        
        // ตรวจสอบข้อมูล
        if (empty($id) || empty($title) || empty($first_name) || empty($last_name) || empty($specialty)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน'];
            break;
        }
        
        // เริ่มสร้าง SQL query
        $sql = "UPDATE frontend_doctors SET 
                title = ?, 
                first_name = ?, 
                last_name = ?, 
                nickname = ?, 
                specialty = ?, 
                education = ?, 
                certification = ?, 
                achievements = ?, 
                additional_features = ?, 
                display_order = ?, 
                is_featured = ?, 
                status = ?";
        
        $params = [$title, $first_name, $last_name, $nickname, $specialty, $education, $certification, $achievements, $additional_features, $display_order, $is_featured, $status];
        $types = "sssssssssiis";
        
        // จัดการไฟล์รูปภาพ (ถ้ามี)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/doctors/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
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
                // ลบรูปเก่า (ถ้ามี)
                $stmt = $conn->prepare("SELECT image_path FROM frontend_doctors WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (!empty($row['image_path']) && file_exists($target_dir . $row['image_path'])) {
                        unlink($target_dir . $row['image_path']);
                    }
                }
                
                // เพิ่ม image_path ในคำสั่ง SQL
                $sql .= ", image_path = ?";
                $params[] = $new_filename;
                $types .= "s";
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
                break;
            }
        }
        
        // เพิ่ม WHERE clause
        $sql .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";
        
        // บันทึกข้อมูล
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'แก้ไขข้อมูลแพทย์เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'delete':
        // ลบข้อมูลแพทย์
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ'];
            break;
        }
        
        // ลบรูปภาพ (ถ้ามี)
        $stmt = $conn->prepare("SELECT image_path FROM frontend_doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['image_path'])) {
                $image_path = "../../img/doctors/" . $row['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // ลบข้อมูล
        $stmt = $conn->prepare("DELETE FROM frontend_doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'ลบข้อมูลแพทย์เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'get':
        // ดึงข้อมูลแพทย์
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("SELECT * FROM frontend_doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // สร้าง HTML สำหรับฟอร์มแก้ไข
            $html = '
            <input type="hidden" name="id" value="'.$data['id'].'">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label required">คำนำหน้า</label>
                    <select class="form-select" name="title" required>
                        <option value="นพ." '.($data['title'] == 'นพ.' ? 'selected' : '').'>นพ.</option>
                        <option value="พญ." '.($data['title'] == 'พญ.' ? 'selected' : '').'>พญ.</option>
                        <option value="ทพ." '.($data['title'] == 'ทพ.' ? 'selected' : '').'>ทพ.</option>
                        <option value="ทพญ." '.($data['title'] == 'ทพญ.' ? 'selected' : '').'>ทพญ.</option>
                        <option value="ดร." '.($data['title'] == 'ดร.' ? 'selected' : '').'>ดร.</option>
                        <option value="ศ.นพ." '.($data['title'] == 'ศ.นพ.' ? 'selected' : '').'>ศ.นพ.</option>
                        <option value="ศ.พญ." '.($data['title'] == 'ศ.พญ.' ? 'selected' : '').'>ศ.พญ.</option>
                        <option value="รศ.นพ." '.($data['title'] == 'รศ.นพ.' ? 'selected' : '').'>รศ.นพ.</option>
                        <option value="รศ.พญ." '.($data['title'] == 'รศ.พญ.' ? 'selected' : '').'>รศ.พญ.</option>
                        <option value="ผศ.นพ." '.($data['title'] == 'ผศ.นพ.' ? 'selected' : '').'>ผศ.นพ.</option>
                        <option value="ผศ.พญ." '.($data['title'] == 'ผศ.พญ.' ? 'selected' : '').'>ผศ.พญ.</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required">ชื่อ</label>
                    <input type="text" class="form-control" name="first_name" value="'.$data['first_name'].'" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label required">นามสกุล</label>
                    <input type="text" class="form-control" name="last_name" value="'.$data['last_name'].'" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ชื่อเล่น</label>
                    <input type="text" class="form-control" name="nickname" value="'.$data['nickname'].'">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label required">ความเชี่ยวชาญ</label>
                    <input type="text" class="form-control" name="specialty" value="'.$data['specialty'].'" required>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">สถาบันการศึกษา</label>
                    <input type="text" class="form-control" name="education" value="'.$data['education'].'">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">ลำดับการแสดงผล</label>
                    <input type="number" class="form-control" name="display_order" value="'.$data['display_order'].'" min="0">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label d-block">สถานะและตัวเลือก</label>
                    <div class="form-check form-switch form-check-inline mt-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="edit_is_featured" value="1" '.($data['is_featured'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_is_featured">แพทย์แนะนำ</label>
                    </div>
                    <div class="form-check form-switch form-check-inline">
                        <input class="form-check-input" type="checkbox" name="status" id="edit_status_active" value="1" '.($data['status'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_active">เปิดใช้งาน</label>
                    </div>
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">ใบรับรองและวุฒิบัตร</label>
                    <input type="text" class="form-control" name="certification" value="'.$data['certification'].'" placeholder="เช่น Board Certified, American Board, ฯลฯ">
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">ผลงานและประสบการณ์ (แยกแต่ละบรรทัด)</label>
                    <textarea class="form-control" name="achievements" rows="3" placeholder="เช่น:
ประสบการณ์การรักษามากกว่า 10,000 เคส
วิทยากรด้านความงามระดับนานาชาติ
ผู้เชี่ยวชาญด้านเทคโนโลยีเลเซอร์รุ่นใหม่">'.$data['achievements'].'</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">คุณสมบัติเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                    <textarea class="form-control" name="additional_features" rows="3" placeholder="เช่น:
เชี่ยวชาญด้านการรักษาริ้วรอย
เชี่ยวชาญการรักษาสิวและรอยแผลเป็น
เชี่ยวชาญการฟื้นฟูผิวหน้าด้วยเทคโนโลยีล่าสุด">'.$data['additional_features'].'</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">รูปภาพ (ถ้าต้องการเปลี่ยน)</label>
                    <input type="file" class="form-control" name="image" id="edit_doctor_image" accept="image/*">
                    <small class="text-muted">ถ้าไม่อัปโหลดจะใช้รูปเดิม</small>
                    <input type="hidden" id="current_image_path" value="'.$data['image_path'].'">
                    <div id="edit_image_preview" class="mt-2"></div>
                </div>
            </div>
            ';
            
            $response = [
                'success' => true, 
                'data' => $data,
                'html' => $html
            ];
        } else {
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลแพทย์'];
        }
        
        break;
        
    case 'quick_view':
        // ดึงข้อมูลแพทย์แบบเร็ว
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("SELECT * FROM frontend_doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // กำหนดรูปภาพ
            $imgPath = $data['image_path'] ?? 'doctor.jpg';
            $imgUrl = "../../img/doctors/{$imgPath}";
            
            // สร้าง HTML ข้อมูลสำหรับแสดงใน Quick View
            $html = '
            <div class="row">
                <div class="col-md-5 text-center mb-3 mb-md-0">
                    <img src="'.$imgUrl.'" alt="'.$data['title'].$data['first_name'].' '.$data['last_name'].'" class="img-fluid rounded">
                </div>
                <div class="col-md-7">
                    <h5 class="mb-1">'.$data['title'].$data['first_name'].' '.$data['last_name'].'</h5>
                    <p class="mb-2 small text-muted">'.($data['nickname'] ? 'หมอ'.$data['nickname'] : '').'</p>
                    
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-'.($data['status'] == 1 ? 'success' : 'secondary').'">'.($data['status'] == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน').'</span>
                        '.($data['is_featured'] ? '<span class="badge bg-primary">แพทย์แนะนำ</span>' : '').'
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">ความเชี่ยวชาญ</h6>
                        <p class="mb-0">'.$data['specialty'].'</p>
                    </div>
                    
                    '.($data['education'] ? '
                    <div class="mb-3">
                        <h6 class="mb-2">สถาบันการศึกษา</h6>
                        <p class="mb-0">'.$data['education'].'</p>
                    </div>' : '').'
                    
                    '.($data['certification'] ? '
                    <div class="mb-3">
                        <h6 class="mb-2">ใบรับรองและวุฒิบัตร</h6>
                        <p class="mb-0">'.$data['certification'].'</p>
                    </div>' : '').'
                    
                    '.($data['achievements'] ? '
                    <div class="mb-3">
                        <h6 class="mb-2">ผลงานและประสบการณ์</h6>
                        <ul class="ps-3 mb-0">
                            '.implode('', array_map(function($achievement) {
                                return '<li>'.trim($achievement).'</li>';
                            }, explode("\n", $data['achievements']))).'
                        </ul>
                    </div>' : '').'
                    
                    '.($data['additional_features'] ? '
                    <div class="mb-3">
                        <h6 class="mb-2">คุณสมบัติเพิ่มเติม</h6>
                        <ul class="ps-3 mb-0">
                            '.implode('', array_map(function($feature) {
                                return '<li>'.trim($feature).'</li>';
                            }, explode("\n", $data['additional_features']))).'
                        </ul>
                    </div>' : '').'
                    
                    <div class="mt-3">
                        <h6 class="mb-2">การแสดงผล</h6>
                        <p class="mb-0">ลำดับการแสดงผล: '.$data['display_order'].'</p>
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
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลแพทย์'];
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
        
        $stmt = $conn->prepare("UPDATE frontend_doctors SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        
        if ($stmt->execute()) {
            $statusText = $status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$statusText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        
        break;

    case 'toggle_featured':
        // เปลี่ยนสถานะแพทย์แนะนำ
        $id = $_POST['id'] ?? 0;
        $featured = $_POST['featured'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการเปลี่ยนสถานะ'];
            break;
        }
        
        $stmt = $conn->prepare("UPDATE frontend_doctors SET is_featured = ? WHERE id = ?");
        $stmt->bind_param("ii", $featured, $id);
        
        if ($stmt->execute()) {
            $featuredText = $featured == 1 ? 'แพทย์แนะนำ' : 'แพทย์ทั่วไป';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$featuredText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        
        break;
}

echo json_encode($response);
exit;
?>