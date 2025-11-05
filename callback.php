<?php
session_start(); // ✅ ต้องมีบรรทัดนี้ก่อนทุกอย่าง

require 'config.php';

if (!isset($_GET['code'])) {
    die('No code provided');
}

$code = $_GET['code'];

// ✅ ขอ token จาก Discord
$token_url = 'https://discord.com/api/oauth2/token';
$data = [
    'client_id' => DISCORD_CLIENT_ID,
    'client_secret' => DISCORD_CLIENT_SECRET,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => DISCORD_REDIRECT_URI,
    'scope' => 'identify email'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true);
if (!isset($token['access_token'])) {
    die('Failed to get access token: ' . htmlspecialchars($response));
}

// ✅ ดึงข้อมูลผู้ใช้จาก Discord
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://discord.com/api/users/@me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token['access_token']]);
$user_info_raw = curl_exec($ch);
curl_close($ch);

$user = json_decode($user_info_raw, true);
if (!isset($user['id'])) {
    die('Failed to fetch user: ' . htmlspecialchars($user_info_raw));
}

// ✅ สร้างลิงก์รูป Avatar
$avatar = '';
if (!empty($user['avatar'])) {
    $ext = strpos($user['avatar'], 'a_') === 0 ? 'gif' : 'png';
    $avatar = "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.{$ext}";
} else {
    $avatar = "https://cdn.discordapp.com/embed/avatars/" . rand(0, 5) . ".png";
}

// ✅ บันทึกลงฐานข้อมูล
$discord_id = $mysqli->real_escape_string($user['id']);
$username   = $mysqli->real_escape_string($user['username']);
$avatar_db  = $mysqli->real_escape_string($avatar);

// ถ้ายังไม่มี → เพิ่ม, ถ้ามีแล้ว → อัปเดตชื่อและรูป
$stmt = $mysqli->prepare("
    INSERT INTO users (discord_id, username, avatar, points)
    VALUES (?, ?, ?, 0)
    ON DUPLICATE KEY UPDATE username = VALUES(username), avatar = VALUES(avatar)
");
$stmt->bind_param('sss', $discord_id, $username, $avatar_db);
$stmt->execute();
$stmt->close();

// ✅ ดึงข้อมูลผู้ใช้ล่าสุด (รวมพ้อย)
$stmt = $mysqli->prepare("SELECT * FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ✅ เซฟข้อมูลใส่ session
$_SESSION['user'] = [
    'id' => $userData['discord_id'],
    'username' => $userData['username'],
    'avatar' => $userData['avatar'],
    'points' => $userData['points']
];

// ✅ กลับไปหน้าหลัก
header('Location: index.php');
exit;
?>
