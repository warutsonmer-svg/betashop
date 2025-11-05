<?php
require_once 'config.php';
require_once 'lib/Auth.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$discord_id = $user['id'];

if (!in_array($discord_id, $admin_ids)) {
    die('<h2 style="color:red;text-align:center;">❌ คุณไม่มีสิทธิ์เข้าหน้านี้</h2>');
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | BETASHOP</title>
<style>
body {
    font-family: 'Prompt', sans-serif;
    background: #0f0f17;
    color: #fff;
    margin: 0;
}
h1 {
    text-align: center;
    background: #191933;
    padding: 15px;
    margin: 0;
}
.menu {
    background: #14142b;
    display: flex;
    justify-content: center;
    gap: 20px;
    padding: 15px;
    border-bottom: 2px solid #222;
}
.menu a {
    color: #fff;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 6px;
    background: #2a2a4a;
    transition: 0.2s;
}
.menu a:hover {
    background: #4e46e5;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    text-align: center;
}
</style>
</head>
<body>
<h1>🛠️ ระบบหลังบ้าน BETASHOP</h1>

<div class="menu">
  <a href="admin/products.php">🛒 จัดการสินค้า</a>
  <a href="admin/users.php">👥 จัดการผู้ใช้</a>
  <a href="admin/topups.php">💰 การเติมเงิน</a>
  <a href="index.php">🏠 กลับหน้าหลัก</a>
</div>

<div class="container">
  <h2>👋 สวัสดี <?= htmlspecialchars($user['username']) ?> (Admin)</h2>
  <p>เลือกเมนูด้านบนเพื่อจัดการระบบหลังบ้าน</p>
</div>

</body>
</html>
