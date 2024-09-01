<?php
session_start();
require_once 'config.php';
require_once 'dbcon.php';

if ($_GET['state'] !== $_SESSION['oauth_state']) {
    die('Invalid state parameter');
}

$token_url = 'https://api.line.me/oauth2/v2.1/token';
$data = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'redirect_uri' => LINE_CALLBACK_URL,
    'client_id' => LINE_CHANNEL_ID,
    'client_secret' => LINE_CHANNEL_SECRET
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($token_url, false, $context);

if ($result === FALSE) {
    die('Failed to get access token');
}

$token_info = json_decode($result, true);

// Get user profile
$profile_url = 'https://api.line.me/v2/profile';
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer " . $token_info['access_token'] . "\r\n",
        'method'  => 'GET'
    )
);
$context  = stream_context_create($options);
$profile = file_get_contents($profile_url, false, $context);

if ($profile === FALSE) {
    die('Failed to get user profile');
}

$user_info = json_decode($profile, true);

// Check if user exists in database
$stmt = $conn->prepare("SELECT * FROM customer WHERE line_user_id = ?");
$stmt->bind_param("s", $user_info['userId']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, update information
    $user = $result->fetch_assoc();
    $stmt = $conn->prepare("UPDATE customer SET line_display_name = ?, line_picture_url = ? WHERE line_user_id = ?");
    $stmt->bind_param("sss", $user_info['displayName'], $user_info['pictureUrl'], $user_info['userId']);
    $stmt->execute();
    $_SESSION['users_id'] = $user['cus_id'];
} else {
    // New user, insert into database
    $stmt = $conn->prepare("INSERT INTO customer (line_user_id, line_display_name, line_picture_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_info['userId'], $user_info['displayName'], $user_info['pictureUrl']);
    $stmt->execute();
    $_SESSION['users_id'] = $conn->insert_id;
}

// Redirect to user dashboard or home page
header('Location: users/index.php');
exit;