<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

// ตรวจสอบการเรียกใช้งาน API
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'ไม่ระบุการกระทำ (action)'];

switch ($action) {
    case 'get_all':
        // ดึงข้อมูล Hero Slides ทั้งหมด
        $sql = "SELECT * FROM frontend_hero_slides ORDER BY display_order ASC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $slides = [];
            while ($row = $result->fetch_assoc()) {
                // แปลง JSON fields กลับเป็น array
                $row['stats_json'] = json_decode($row['stats_json'], true) ?: [];
                $row['features_json'] = json_decode($row['features_json'], true) ?: [];
                $row['buttons_json'] = json_decode($row['buttons_json'], true) ?: [];
                $slides[] = $row;
            }
            $response = ['success' => true, 'data' => $slides];
        } else {
            $response = ['success' => true, 'data' => [], 'message' => 'ไม่พบข้อมูล Hero Slides'];
        }
        break;
        
    case 'get':
        // ดึงข้อมูล Hero Slide
        $id = $_GET['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการดึงข้อมูล'];
            break;
        }
        
        $stmt = $conn->prepare("SELECT * FROM frontend_hero_slides WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // แปลง JSON fields กลับเป็น array
            $data['stats_json'] = json_decode($data['stats_json'], true) ?: [];
            $data['features_json'] = json_decode($data['features_json'], true) ?: [];
            $data['buttons_json'] = json_decode($data['buttons_json'], true) ?: [];
            
            // สร้าง HTML สำหรับฟอร์มแก้ไข
            $html = '
            <input type="hidden" name="id" value="'.$data['id'].'">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">หัวข้อย่อย (Subtitle)</label>
                    <input type="text" class="form-control" name="subtitle" value="'.$data['subtitle'].'">
                    <small class="text-muted">เช่น "Welcome to D Care Clinic"</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">หัวข้อหลัก (Title)</label>
                    <input type="text" class="form-control" name="title" value="'.$data['title'].'" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ส่วนที่เน้นในหัวข้อหลัก (Highlight)</label>
                    <input type="text" class="form-control" name="title_highlight" value="'.$data['title_highlight'].'">
                    <small class="text-muted">จะแสดงเป็นสีไล่ระดับ (Gradient)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ลำดับการแสดงผล</label>
                    <input type="number" class="form-control" name="display_order" value="'.$data['display_order'].'" min="0">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">คำอธิบาย</label>
                    <textarea class="form-control" name="description" rows="2">'.$data['description'].'</textarea>
                    <small class="text-muted">สามารถใช้ HTML tag &lt;br&gt; เพื่อขึ้นบรรทัดใหม่ได้</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">รูปภาพพื้นหลัง</label>
                    <input type="file" class="form-control" name="background_image" accept="image/*">
                    '.(!empty($data['background_image']) ? '<div class="mt-2"><img src="../../'.$data['background_image'].'" class="img-thumbnail" style="max-height: 100px"></div>' : '').'
                    <input type="hidden" name="current_background_image" value="'.$data['background_image'].'">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">รูปภาพด้านขวา (Hero)</label>
                    <input type="file" class="form-control" name="hero_image" accept="image/*">
                    '.(!empty($data['hero_image']) ? '<div class="mt-2"><img src="../../'.$data['hero_image'].'" class="img-thumbnail" style="max-height: 100px"></div>' : '').'
                    <input type="hidden" name="current_hero_image" value="'.$data['hero_image'].'">
                </div>
                <div class="col-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" '.($data['is_active'] ? 'checked' : '').'>
                        <label class="form-check-label" for="is_active">เปิดใช้งาน (แสดงบนหน้าเว็บไซต์)</label>
                    </div>
                </div>
                
                <!-- ส่วนจัดการสถิติ -->
                <div class="col-12 mb-3">
                    <label class="form-label">สถิติ (Stats)</label>
                    <div id="stats-container">
                    ';
                    
                    if (!empty($data['stats_json'])) {
                        foreach ($data['stats_json'] as $index => $stat) {
                            $html .= '
                            <div class="row mb-2 stat-item">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="stats[label][]" placeholder="ชื่อสถิติ" value="'.$stat['label'].'">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="stats[value][]" placeholder="ค่า" value="'.$stat['value'].'">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="stats[suffix][]" placeholder="หน่วย/คำต่อท้าย" value="'.$stat['suffix'].'">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-danger remove-stat">ลบ</button>
                                </div>
                            </div>';
                        }
                    }
                    
                    $html .= '
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="add-stat">
                        <i class="ri-add-line"></i> เพิ่มสถิติ
                    </button>
                </div>
                
                <!-- ส่วนจัดการ features -->
                <div class="col-12 mb-3">
                    <label class="form-label">คุณสมบัติ (Features)</label>
                    <div id="features-container">
                    ';
                    
                    if (!empty($data['features_json'])) {
                        foreach ($data['features_json'] as $index => $feature) {
                            $html .= '
                            <div class="row mb-2 feature-item">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="features[text][]" placeholder="ข้อความ" value="'.$feature['text'].'">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="features[icon][]" placeholder="ไอคอน" value="'.$feature['icon'].'">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-danger remove-feature">ลบ</button>
                                </div>
                            </div>';
                        }
                    }
                    
                    $html .= '
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="add-feature">
                        <i class="ri-add-line"></i> เพิ่มคุณสมบัติ
                    </button>
                </div>
                
                <!-- ส่วนจัดการปุ่ม -->
                <div class="col-12 mb-3">
                    <label class="form-label">ปุ่ม (Buttons)</label>
                    <div id="buttons-container">
                    ';
                    
                    if (!empty($data['buttons_json'])) {
                        foreach ($data['buttons_json'] as $index => $button) {
                            $html .= '
                            <div class="row mb-2 button-item">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="buttons[text][]" placeholder="ข้อความ" value="'.$button['text'].'">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="buttons[icon][]" placeholder="ไอคอน" value="'.$button['icon'].'">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="buttons[url][]" placeholder="URL" value="'.$button['url'].'">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" name="buttons[type][]">
                                        <option value="primary" '.($button['type'] == 'primary' ? 'selected' : '').'>หลัก</option>
                                        <option value="secondary" '.($button['type'] == 'secondary' ? 'selected' : '').'>รอง</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger remove-button">ลบ</button>
                                </div>
                            </div>';
                        }
                    }
                    
                    $html .= '
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="add-button">
                        <i class="ri-add-line"></i> เพิ่มปุ่ม
                    </button>
                </div>
            </div>
            
            <script>
                // เพิ่ม Event Listener สำหรับปุ่มเพิ่ม/ลบ
                document.getElementById("add-stat").addEventListener("click", function() {
                    const container = document.getElementById("stats-container");
                    const newItem = document.createElement("div");
                    newItem.className = "row mb-2 stat-item";
                    newItem.innerHTML = `
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="stats[label][]" placeholder="ชื่อสถิติ">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="stats[value][]" placeholder="ค่า">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="stats[suffix][]" placeholder="หน่วย/คำต่อท้าย">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-danger remove-stat">ลบ</button>
                        </div>
                    `;
                    container.appendChild(newItem);
                    
                    // เพิ่ม Event Listener สำหรับปุ่มลบที่สร้างใหม่
                    newItem.querySelector(".remove-stat").addEventListener("click", function() {
                        container.removeChild(newItem);
                    });
                });
                
                // Event Listener สำหรับปุ่มลบสถิติที่มีอยู่แล้ว
                document.querySelectorAll(".remove-stat").forEach(function(button) {
                    button.addEventListener("click", function() {
                        const item = this.closest(".stat-item");
                        item.parentNode.removeChild(item);
                    });
                });
                
                // ทำเช่นเดียวกันสำหรับ features และ buttons
                document.getElementById("add-feature").addEventListener("click", function() {
                    const container = document.getElementById("features-container");
                    const newItem = document.createElement("div");
                    newItem.className = "row mb-2 feature-item";
                    newItem.innerHTML = `
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="features[text][]" placeholder="ข้อความ">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="features[icon][]" placeholder="ไอคอน">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-danger remove-feature">ลบ</button>
                        </div>
                    `;
                    container.appendChild(newItem);
                    
                    newItem.querySelector(".remove-feature").addEventListener("click", function() {
                        container.removeChild(newItem);
                    });
                });
                
                document.querySelectorAll(".remove-feature").forEach(function(button) {
                    button.addEventListener("click", function() {
                        const item = this.closest(".feature-item");
                        item.parentNode.removeChild(item);
                    });
                });
                
                document.getElementById("add-button").addEventListener("click", function() {
                    const container = document.getElementById("buttons-container");
                    const newItem = document.createElement("div");
                    newItem.className = "row mb-2 button-item";
                    newItem.innerHTML = `
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="buttons[text][]" placeholder="ข้อความ">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="buttons[icon][]" placeholder="ไอคอน">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="buttons[url][]" placeholder="URL">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="buttons[type][]">
                                <option value="primary">หลัก</option>
                                <option value="secondary">รอง</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger remove-button">ลบ</button>
                        </div>
                    `;
                    container.appendChild(newItem);
                    
                    newItem.querySelector(".remove-button").addEventListener("click", function() {
                        container.removeChild(newItem);
                    });
                });
                
                document.querySelectorAll(".remove-button").forEach(function(button) {
                    button.addEventListener("click", function() {
                        const item = this.closest(".button-item");
                        item.parentNode.removeChild(item);
                    });
                });
            </script>
            ';
            
            $response = [
                'success' => true, 
                'data' => $data,
                'html' => $html
            ];
        } else {
            $response = ['success' => false, 'message' => 'ไม่พบข้อมูล Hero Slide'];
        }
        break;
        
    case 'update':
        // แก้ไข Hero Slide
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการแก้ไข'];
            break;
        }
        
        // รวบรวมข้อมูลจากฟอร์ม
        $subtitle = $_POST['subtitle'] ?? null;
        $title = $_POST['title'] ?? '';
        $title_highlight = $_POST['title_highlight'] ?? null;
        $description = $_POST['description'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // สร้าง JSON จากข้อมูลรูปแบบอาร์เรย์
        $stats_json = '[]';
        if (isset($_POST['stats']) && !empty($_POST['stats']['label'])) {
            $stats = [];
            foreach ($_POST['stats']['label'] as $index => $label) {
                if (!empty($label)) {
                    $stats[] = [
                        'label' => $label,
                        'value' => $_POST['stats']['value'][$index] ?? '',
                        'suffix' => $_POST['stats']['suffix'][$index] ?? ''
                    ];
                }
            }
            $stats_json = json_encode($stats, JSON_UNESCAPED_UNICODE);
        }
        
        $features_json = '[]';
        if (isset($_POST['features']) && !empty($_POST['features']['text'])) {
            $features = [];
            foreach ($_POST['features']['text'] as $index => $text) {
                if (!empty($text)) {
                    $features[] = [
                        'text' => $text,
                        'icon' => $_POST['features']['icon'][$index] ?? ''
                    ];
                }
            }
            $features_json = json_encode($features, JSON_UNESCAPED_UNICODE);
        }
        
        $buttons_json = '[]';
        if (isset($_POST['buttons']) && !empty($_POST['buttons']['text'])) {
            $buttons = [];
            foreach ($_POST['buttons']['text'] as $index => $text) {
                if (!empty($text)) {
                    $buttons[] = [
                        'text' => $text,
                        'icon' => $_POST['buttons']['icon'][$index] ?? '',
                        'url' => $_POST['buttons']['url'][$index] ?? '#',
                        'type' => $_POST['buttons']['type'][$index] ?? 'primary'
                    ];
                }
            }
            $buttons_json = json_encode($buttons, JSON_UNESCAPED_UNICODE);
        }
        
        // สร้าง SQL query พื้นฐาน
        $sql = "UPDATE frontend_hero_slides SET 
                subtitle = ?, 
                title = ?, 
                title_highlight = ?, 
                description = ?, 
                display_order = ?, 
                is_active = ?, 
                stats_json = ?, 
                features_json = ?, 
                buttons_json = ?";
        
        $params = [$subtitle, $title, $title_highlight, $description, $display_order, $is_active, $stats_json, $features_json, $buttons_json];
        $types = "ssssissss";  // s = string, i = integer
        
        // จัดการไฟล์รูปภาพพื้นหลัง (ถ้ามี)
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
            $target_dir = "../../img/pr/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["background_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            $file_path = "img/pr/" . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $target_file)) {
                // เพิ่มในคำสั่ง SQL
                $sql .= ", background_image = ?";
                $params[] = $file_path;
                $types .= "s";
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพพื้นหลัง'];
                break;
            }
        }
        
        // จัดการไฟล์รูปภาพ Hero (ถ้ามี)
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
            $target_dir = "../../img/pr/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["hero_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            $file_path = "img/pr/" . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (move_uploaded_file($_FILES["hero_image"]["tmp_name"], $target_file)) {
                // เพิ่มในคำสั่ง SQL
                $sql .= ", hero_image = ?";
                $params[] = $file_path;
                $types .= "s";
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ Hero'];
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
            $response = ['success' => true, 'message' => 'แก้ไขข้อมูล Hero Slide เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        break;
        
    case 'add':
        // เพิ่ม Hero Slide ใหม่
        $subtitle = $_POST['subtitle'] ?? null;
        $title = $_POST['title'] ?? '';
        $title_highlight = $_POST['title_highlight'] ?? null;
        $description = $_POST['description'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($title)) {
            $response = ['success' => false, 'message' => 'กรุณากรอกหัวข้อหลัก (Title)'];
            break;
        }
        
        // สร้าง JSON จากข้อมูลรูปแบบอาร์เรย์
        $stats_json = '[]';
        if (isset($_POST['stats']) && !empty($_POST['stats']['label'])) {
            $stats = [];
            foreach ($_POST['stats']['label'] as $index => $label) {
                if (!empty($label)) {
                    $stats[] = [
                        'label' => $label,
                        'value' => $_POST['stats']['value'][$index] ?? '',
                        'suffix' => $_POST['stats']['suffix'][$index] ?? ''
                    ];
                }
            }
            $stats_json = json_encode($stats, JSON_UNESCAPED_UNICODE);
        }
        
        $features_json = '[]';
        if (isset($_POST['features']) && !empty($_POST['features']['text'])) {
            $features = [];
            foreach ($_POST['features']['text'] as $index => $text) {
                if (!empty($text)) {
                    $features[] = [
                        'text' => $text,
                        'icon' => $_POST['features']['icon'][$index] ?? ''
                    ];
                }
            }
            $features_json = json_encode($features, JSON_UNESCAPED_UNICODE);
        }
        
        $buttons_json = '[]';
        if (isset($_POST['buttons']) && !empty($_POST['buttons']['text'])) {
            $buttons = [];
            foreach ($_POST['buttons']['text'] as $index => $text) {
                if (!empty($text)) {
                    $buttons[] = [
                        'text' => $text,
                        'icon' => $_POST['buttons']['icon'][$index] ?? '',
                        'url' => $_POST['buttons']['url'][$index] ?? '#',
                        'type' => $_POST['buttons']['type'][$index] ?? 'primary'
                    ];
                }
            }
            $buttons_json = json_encode($buttons, JSON_UNESCAPED_UNICODE);
        }
        
        // จัดการไฟล์รูปภาพพื้นหลัง
        $background_image = null;
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
            $target_dir = "../../img/pr/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["background_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            $background_image = "img/pr/" . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (!move_uploaded_file($_FILES["background_image"]["tmp_name"], $target_file)) {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพพื้นหลัง'];
                break;
            }
        }
        
        // จัดการไฟล์รูปภาพ Hero
        $hero_image = null;
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
            $target_dir = "../../img/pr/";
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["hero_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            $hero_image = "img/pr/" . $new_filename;
            
            // ตรวจสอบชนิดไฟล์
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                $response = ['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPG, JPEG, PNG และ GIF เท่านั้น'];
                break;
            }
            
            // ย้ายไฟล์
            if (!move_uploaded_file($_FILES["hero_image"]["tmp_name"], $target_file)) {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ Hero'];
                break;
            }
        }
        
        // เตรียม SQL สำหรับการเพิ่มข้อมูล
        $sql = "INSERT INTO frontend_hero_slides (subtitle, title, title_highlight, description, background_image, hero_image, stats_json, features_json, buttons_json, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // บันทึกข้อมูล
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssii", $subtitle, $title, $title_highlight, $description, $background_image, $hero_image, $stats_json, $features_json, $buttons_json, $display_order, $is_active);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'เพิ่ม Hero Slide เรียบร้อยแล้ว', 'id' => $stmt->insert_id];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error];
        }
        break;
        
    case 'delete':
        // ลบ Hero Slide
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            $response = ['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ'];
            break;
        }
        
        // ตรวจสอบว่ามีข้อมูลและรูปภาพที่ต้องลบหรือไม่
        $stmt = $conn->prepare("SELECT background_image, hero_image FROM frontend_hero_slides WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // ลบรูปภาพ (ถ้ามี)
            if (!empty($data['background_image'])) {
                $bg_img_path = "../../" . $data['background_image'];
                if (file_exists($bg_img_path)) {
                    unlink($bg_img_path);
                }
            }
            
            if (!empty($data['hero_image'])) {
                $hero_img_path = "../../" . $data['hero_image'];
                if (file_exists($hero_img_path)) {
                    unlink($hero_img_path);
                }
            }
        }
        
        // ลบข้อมูลจากฐานข้อมูล
        $stmt = $conn->prepare("DELETE FROM frontend_hero_slides WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'ลบ Hero Slide เรียบร้อยแล้ว'];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error];
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
        
        $stmt = $conn->prepare("UPDATE frontend_hero_slides SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        
        if ($stmt->execute()) {
            $statusText = $status == 1 ? 'แสดง' : 'ซ่อน';
            $response = ['success' => true, 'message' => "เปลี่ยนสถานะเป็น{$statusText}เรียบร้อยแล้ว"];
        } else {
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ: ' . $stmt->error];
        }
        break;
}

echo json_encode($response);
exit;
?>