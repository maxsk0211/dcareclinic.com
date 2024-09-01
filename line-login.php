<?php
session_start();
require_once 'config.php';  // ไฟล์ที่เก็บค่า LINE Channel ID และ Channel Secret

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$authorize_url = "https://access.line.me/oauth2/v2.1/authorize";
$params = array(
    'response_type' => 'code',
    'client_id' => LINE_CHANNEL_ID,
    'redirect_uri' => LINE_CALLBACK_URL,
    'state' => $state,
    'scope' => 'profile openid email'
);

$url = $authorize_url . '?' . http_build_query($params);
header('Location: ' . $url);
exit;