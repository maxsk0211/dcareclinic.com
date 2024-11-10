<?php
function checkPagePermission($page) {
    global $conn;
    
    // ถ้าเป็น admin ให้เข้าถึงได้ทุกหน้า
    if ($_SESSION['position_id'] == 1) {
        return true;
    }

    // ตรวจสอบสิทธิ์จากตำแหน่งและสิทธิ์พิเศษ
    $sql = "SELECT 
            CASE 
                WHEN rp.granted = 1 THEN true -- มีสิทธิ์จากตำแหน่ง
                WHEN usp.granted = 1 THEN true -- มีสิทธิ์พิเศษ
                ELSE false
            END as has_permission
            FROM permissions p
            LEFT JOIN role_permissions rp ON p.permission_id = rp.permission_id 
                AND rp.position_id = ?
            LEFT JOIN user_specific_permissions usp ON p.permission_id = usp.permission_id 
                AND usp.users_id = ?
                AND (usp.end_date IS NULL OR usp.end_date >= CURDATE())
            WHERE p.page = ?
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", 
        $_SESSION['position_id'],
        $_SESSION['users_id'],
        $page
    );
    
    if (!$stmt->execute()) {
        // Log error
        error_log("Permission check failed: " . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    $permission = $result->fetch_assoc();

    return $permission && $permission['has_permission'] == 1;
}

function hasSpecificPermission($permission_name) {
    global $conn;
    
    // ถ้าเป็น admin
    if ($_SESSION['position_id'] == 1) {
        return true;
    }

    $sql = "SELECT 
            CASE 
                WHEN rp.granted = 1 THEN true
                WHEN usp.granted = 1 AND (usp.end_date IS NULL OR usp.end_date >= CURDATE()) THEN true
                ELSE false
            END as has_permission
            FROM permissions p
            LEFT JOIN role_permissions rp ON p.permission_id = rp.permission_id 
                AND rp.position_id = ?
            LEFT JOIN user_specific_permissions usp ON p.permission_id = usp.permission_id 
                AND usp.users_id = ?
            WHERE p.action = ?
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error);
        return false;
    }

    $stmt->bind_param("iis", 
        $_SESSION['position_id'],
        $_SESSION['users_id'],
        $permission_name
    );
    
    if (!$stmt->execute()) {
        error_log("Permission check failed: " . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    $permission = $result->fetch_assoc();

    // Debug log
    error_log("Checking permission: " . $permission_name . 
              " for user: " . $_SESSION['users_id'] . 
              " with position: " . $_SESSION['position_id'] . 
              " result: " . var_export($permission, true));

    return $permission && $permission['has_permission'] == 1;
}