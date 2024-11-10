<?php
session_start();
require '../dbcon.php';
require_once 'check_permission.php';

if (!isset($_SESSION['users_id'])) {
    header("Location: ../index.php");
    exit;
}

// ตรวจสอบสิทธิ์การเข้าถึงหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
if (!checkPagePermission($current_page)) {
    // เก็บชื่อหน้าที่พยายามเข้าถึงใน session
    $_SESSION['attempted_page'] = $current_page;
    header("Location: error-permission.php");
    exit;
}

?>