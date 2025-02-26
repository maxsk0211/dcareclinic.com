<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

// ตรวจสอบการเรียกใช้งาน API
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'ไม่ระบุการกระทำ (action)'];

switch ($action) {
    case 'add':
        // เพิ่มโปรโมชั่นใหม่
        $course_id = $_POST['course_id'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? null;
        $original_price = !empty($_POST['original_price']) ? $_POST['original_price'] : null;
        $promotion_price = $_POST['promotion_price'] ?? 0;
        $discount_percent = !empty($_POST['discount_percent']) ? $_POST['discount_percent'] : null;
        $badge_text = $_POST['badge_text'] ?? null;
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $features = $_POST['features'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        
        // ตรวจสอบข้อมูล
        if (empty($course_id) || empty($title) || empty($promotion_price) || empty($start_date) || empty($end_date)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน'];
            break;
        }
        
        // คำนวณ discount_percent อัตโนมัติถ้าไม่ได้ระบุ
        if ($original_price && empty($discount_percent)) {
            $discount = (($original_price - $promotion_price) / $original_price) * 100;
            $discount_percent = round($discount, 2);
        }
        
        // ตรวจสอบวันที่
        if (strtotime($end_date) < strtotime($start_date)) {
            $response = ['success' => false, 'message' => 'วันสิ้นสุดต้องมากกว่าวันเริ่มต้น'];
            break;
        }
        
        // จัดการไฟล์รูปภาพ (ถ้ามี)
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/promotion/";
            
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
		$stmt = $conn->prepare("INSERT INTO frontend_promotions (course_id, title, description, original_price, promotion_price, discount_percent, badge_text, start_date, end_date, features, image_path, display_order, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

		// แก้ไขให้มี type definition 14 ตัวอักษร ตรงกับจำนวนตัวแปร
		$stmt->bind_param("issdddssssiiii", $course_id, $title, $description, $original_price, $promotion_price, $discount_percent, $badge_text, $start_date, $end_date, $features, $image_path, $display_order, $is_featured, $status);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว', 'id' => $stmt->insert_id];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'update':
        // แก้ไขโปรโมชั่น
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? null;
        $original_price = !empty($_POST['original_price']) ? $_POST['original_price'] : null;
        $promotion_price = $_POST['promotion_price'] ?? 0;
        $discount_percent = !empty($_POST['discount_percent']) ? $_POST['discount_percent'] : null;
        $badge_text = $_POST['badge_text'] ?? null;
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $features = $_POST['features'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        
        // ตรวจสอบข้อมูล
        if (empty($id) || empty($title) || empty($promotion_price) || empty($start_date) || empty($end_date)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน'];
            break;
        }
        
        // คำนวณ discount_percent อัตโนมัติถ้าไม่ได้ระบุ
        if ($original_price && empty($discount_percent)) {
            $discount = (($original_price - $promotion_price) / $original_price) * 100;
            $discount_percent = round($discount, 2);
        }
        
        // ตรวจสอบวันที่
        if (strtotime($end_date) < strtotime($start_date)) {
            $response = ['success' => false, 'message' => 'วันสิ้นสุดต้องมากกว่าวันเริ่มต้น'];
            break;
        }
        
        // เริ่มสร้าง SQL query
		$sql = "UPDATE frontend_promotions SET 
		        title = ?, 
		        description = ?, 
		        original_price = ?, 
		        promotion_price = ?, 
		        discount_percent = ?, 
		        badge_text = ?, 
		        start_date = ?, 
		        end_date = ?, 
		        features = ?, 
		        display_order = ?, 
		        is_featured = ?, 
		        status = ?";
        
		$params = [$title, $description, $original_price, $promotion_price, $discount_percent, $badge_text, $start_date, $end_date, $features, $display_order, $is_featured, $status];
		$types = "ssdddssssiiii";  // แก้ไขให้ original_price, promotion_price, discount_percent เป็น d (double)

		$types = "";
		foreach ($params as $param) {
		    if (is_int($param)) {
		        $types .= "i";
		    } elseif (is_float($param) || is_double($param)) {
		        $types .= "d"; 
		    } else {
		        $types .= "s";
		    }
		}
		// echo "Types before image: " . $types . " (len:" . strlen($types) . "), Params: " . count($params) . "\n";

        // จัดการไฟล์รูปภาพ (ถ้ามี)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../../img/promotion/";
            
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
                $stmt = $conn->prepare("SELECT image_path FROM frontend_promotions WHERE id = ?");
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
			    $types .= "s";  // เพิ่ม type s สำหรับ string
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
                break;
            }
        }
        
        // เพิ่ม WHERE clause
		$sql .= " WHERE id = ?";
		$params[] = $id;
		$types .= "i";  // เพิ่ม type i สำหรับ integer id

		// เพิ่มก่อนเรียก bind_param()
		if (strlen($types) !== count($params)) {
		    die("Error: Types (" . strlen($types) . ") and params (" . count($params) . ") count mismatch");
		}
        // บันทึกข้อมูล
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'แก้ไขโปรโมชั่นเรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'delete':
        // ลบโปรโมชั่น
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ'];
            break;
        }
        
        // ลบรูปภาพ (ถ้ามี)
        $stmt = $conn->prepare("SELECT image_path FROM frontend_promotions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['image_path'])) {
                $image_path = "../../img/promotion/" . $row['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // ลบข้อมูล
        $stmt = $conn->prepare("DELETE FROM frontend_promotions WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'ลบโปรโมชั่นเรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error];
        }
        
        break;
        
    case 'get':
        // ดึงข้อมูลโปรโมชั่น
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("
            SELECT fp.*, c.course_name, c.course_pic, c.course_detail
            FROM frontend_promotions fp
            JOIN course c ON fp.course_id = c.course_id
            WHERE fp.id = ?
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
                    <label class="form-label required">ชื่อโปรโมชั่น</label>
                    <input type="text" class="form-control" name="title" value="'.$data['title'].'" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคาปกติ (บาท)</label>
                    <input type="number" class="form-control" name="original_price" step="0.01" value="'.$data['original_price'].'">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">ราคาโปรโมชั่น (บาท)</label>
                    <input type="number" class="form-control" name="promotion_price" step="0.01" value="'.$data['promotion_price'].'" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">เปอร์เซ็นต์ส่วนลด (%)</label>
                    <input type="number" class="form-control" name="discount_percent" step="0.01" value="'.$data['discount_percent'].'" min="0" max="100">
                    <small class="text-muted">ปล่อยว่างเพื่อคำนวณอัตโนมัติ</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ข้อความป้ายกำกับ</label>
                    <input type="text" class="form-control" name="badge_text" value="'.$data['badge_text'].'" placeholder="เช่น HOT DEAL">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ลำดับการแสดงผล</label>
                    <input type="number" class="form-control" name="display_order" value="'.$data['display_order'].'" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">วันเริ่มต้น</label>
                    <input type="date" class="form-control" name="start_date" value="'.$data['start_date'].'" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">วันสิ้นสุด</label>
                    <input type="date" class="form-control" name="end_date" value="'.$data['end_date'].'" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label d-block">สถานะและตัวเลือก</label>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="edit_is_featured" value="1" '.($data['is_featured'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_is_featured">แสดงในส่วน Featured</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="status" id="edit_status_active" value="1" '.($data['status'] == 1 ? 'checked' : '').'>
                        <label class="form-check-label" for="edit_status_active">เปิดใช้งาน</label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">รายละเอียดโปรโมชั่น</label>
                    <textarea class="form-control" name="description" rows="3">'.$data['description'].'</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">คุณสมบัติเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                    <textarea class="form-control" name="features" rows="3" placeholder="เช่น:
รับฟรี! บริการนวดหน้า
รับรองผลลัพธ์ 100%
จองคิววันนี้รับส่วนลดเพิ่ม 5%">'.$data['features'].'</textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">รูปภาพ (ถ้าต้องการเปลี่ยน)</label>
                    <input type="file" class="form-control" name="image" id="edit_promotion_image" accept="image/*">
                    <small class="text-muted">ถ้าไม่อัปโหลดจะใช้รูปเดิม</small>
                    <input type="hidden" id="current_image_path" value="'.$data['image_path'].'">
                    <input type="hidden" id="course_pic" value="'.$data['course_pic'].'">
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
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลโปรโมชั่น'];
        }
        
        break;
        
    case 'quick_view':
        // ดึงข้อมูลโปรโมชั่นแบบเร็ว
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("
            SELECT fp.*, c.course_name, c.course_price, c.course_pic, c.course_detail 
            FROM frontend_promotions fp
            JOIN course c ON fp.course_id = c.course_id
            WHERE fp.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // คำนวณส่วนลดถ้าไม่มี
            if (empty($data['discount_percent']) && !empty($data['original_price']) && !empty($data['promotion_price'])) {
                $discount = (($data['original_price'] - $data['promotion_price']) / $data['original_price']) * 100;
                $data['discount_percent'] = round($discount, 2);
            }
            
            // กำหนดสถานะโปรโมชั่น
            $now = strtotime(date('Y-m-d'));
            $start = strtotime($data['start_date']);
            $end = strtotime($data['end_date']);
            
            $status_text = '';
            $status_class = '';
            if ($now < $start) {
                $status_text = 'กำลังจะมาถึง';
                $status_class = 'bg-info';
            } elseif ($now > $end) {
                $status_text = 'หมดอายุ';
                $status_class = 'bg-secondary';
            } else {
                $status_text = 'กำลังใช้งาน';
                $status_class = 'bg-success';
            }
            
            // กำหนดรูปภาพ
            $imgPath = $data['image_path'] ?? $data['course_pic'] ?? 'promotion.png';
            $imgUrl = "../../img/" . ($data['image_path'] ? "promotion/{$imgPath}" : "course/{$imgPath}");
            
            // สร้าง HTML ข้อมูลสำหรับแสดงใน Quick View
            $html = '
            <div class="row">
                <div class="col-md-5 text-center mb-3 mb-md-0">
                    <img src="'.$imgUrl.'" alt="'.$data['title'].'" class="img-fluid rounded">
                </div>
                <div class="col-md-7">
                    <h5 class="mb-1">'.$data['title'].'</h5>
                    <p class="mb-2 small text-muted">คอร์ส: '.$data['course_name'].'</p>
                    
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge '.$status_class.'">'.$status_text.'</span>
                        '.($data['badge_text'] ? '<span class="badge bg-warning">'.$data['badge_text'].'</span>' : '').'
                        '.($data['is_featured'] ? '<span class="badge bg-primary">โปรโมชั่นแนะนำ</span>' : '').'
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">ราคา</h6>
                        <div class="d-flex align-items-baseline">
                            '.($data['original_price'] ? '<span class="text-decoration-line-through text-muted me-2">'.number_format($data['original_price'], 0).' บาท</span>' : '').'
                            <span class="fs-5 fw-bold text-danger">'.number_format($data['promotion_price'], 0).' บาท</span>
                            '.($data['discount_percent'] ? '<span class="badge bg-danger ms-2">ลด '.$data['discount_percent'].'%</span>' : '').'
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">ระยะเวลาโปรโมชั่น</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <i class="ri-calendar-line text-primary"></i>
                            <span>'.date('d/m/Y', strtotime($data['start_date'])).' - '.date('d/m/Y', strtotime($data['end_date'])).'</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">รายละเอียด</h6>
                        <p class="mb-0">'.($data['description'] ?: '<span class="text-muted">ไม่มีรายละเอียดเพิ่มเติม</span>').'</p>
                    </div>
                    
                    '.($data['features'] ? '
                    <div class="mb-3">
                        <h6 class="mb-2">คุณสมบัติเพิ่มเติม</h6>
                        <ul class="ps-3 mb-0">
                            '.implode('', array_map(function($feature) {
                                return '<li>'.trim($feature).'</li>';
                            }, explode("\n", $data['features']))).'
                        </ul>
                    </div>' : '').'
                    
                    <div class="mt-3">
                        <h6 class="mb-2">การแสดงผล</h6>
                        <p class="mb-0">ลำดับการแสดงผล: '.$data['display_order'].'</p>
                        <p class="mb-0">สถานะ: '.($data['status'] == 1 ? '<span class="text-success">เปิดใช้งาน</span>' : '<span class="text-muted">ปิดใช้งาน</span>').'</p>
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
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูลโปรโมชั่น'];
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
        
        $stmt = $conn->prepare("UPDATE frontend_promotions SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        
        if ($stmt->execute()) {
            $statusText = $status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$statusText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        
        break;

    case 'toggle_featured':
        // เปลี่ยนสถานะโปรโมชั่นแนะนำ
        $id = $_POST['id'] ?? 0;
        $featured = $_POST['featured'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการเปลี่ยนสถานะ'];
            break;
        }
        
        $stmt = $conn->prepare("UPDATE frontend_promotions SET is_featured = ? WHERE id = ?");
        $stmt->bind_param("ii", $featured, $id);
        
        if ($stmt->execute()) {
            $featuredText = $featured == 1 ? 'โปรโมชั่นแนะนำ' : 'โปรโมชั่นทั่วไป';
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