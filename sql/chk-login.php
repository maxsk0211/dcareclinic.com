<?php
session_start();
require_once '../dbcon.php';

// Define constants if not already defined in config.php
if (!defined('ADMIN_POSITION')) define('ADMIN_POSITION', 1);
if (!defined('MANAGER_POSITION')) define('MANAGER_POSITION', 2);
if (!defined('STAFF_POSITION')) define('STAFF_POSITION', 3);
if (!defined('USER_STATUS_ACTIVE')) define('USER_STATUS_ACTIVE', 1);
if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 5);

// Sanitize and validate input
$username = mysqli_real_escape_string($conn, $_POST['users_username']);
$password = mysqli_real_escape_string($conn, $_POST['users_password']);

if (!$username || !$password) {
    setLoginError("กรุณากรอกชื่อผู้ใช้และรหัสผ่าน");
    redirectToLogin();
}

// Prepare SQL statement
$stmt = $conn->prepare("SELECT u.*, p.position_name 
                        FROM users u 
                        JOIN position p ON u.position_id = p.position_id 
                        WHERE u.users_username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_object();
    
    if ($user->users_password == $password) {  // Assuming the password is stored as plain text
        if ($user->users_status == USER_STATUS_ACTIVE) {
            setUserSession($user);
            redirectBasedOnPosition($user->position_id);
        } else {
            setLoginError("ผู้ใช้งานถูกปิด กรุณาติดต่อผู้จัดการ");
            redirectToLogin();
        }
    } else {
        handleFailedLogin($username);
    }
} else {
    handleFailedLogin($username);
}

$stmt->close();
$conn->close();

function setUserSession($user) {
    $_SESSION['users_id'] = $user->users_id;
    $_SESSION['users_fname'] = $user->users_fname;
    $_SESSION['users_lname'] = $user->users_lname;
    $_SESSION['users_username'] = $user->users_username;
    $_SESSION['position_id'] = $user->position_id;
    $_SESSION['position_name'] = $user->position_name;
    if ($user->position_id != ADMIN_POSITION) {
        $_SESSION['branch_id'] = $user->branch_id;
    }
}

function redirectBasedOnPosition($positionId) {
    if ($positionId == ADMIN_POSITION || $positionId == MANAGER_POSITION || $positionId == STAFF_POSITION) {
        header("Location: ../admin/index.php");
    } else {
        // Handle other positions if needed
        header("Location: ../default.php");
    }
    exit();
}

function handleFailedLogin($username) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 1;
    } else {
        $_SESSION['login_attempts']++;
    }

    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        setLoginError("เกินจำนวนครั้งที่พยายามเข้าสู่ระบบ กรุณาลองใหม่ในภายหลัง");
    } else {
        setLoginError("ชื่อผู้ใช้หรือรหัสผ่านของคุณไม่ถูกต้อง");
    }
    $_SESSION['users_username'] = $username;
    redirectToLogin();
}

function setLoginError($message) {
    $_SESSION['login_error'] = $message;
}

function redirectToLogin() {
    header("Location: ../login.php");
    exit();
}