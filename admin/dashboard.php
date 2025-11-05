<?php
require '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['id'], $admin_ids)) {
  header("Location: ../index.php");
  exit;
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ระบบหลังบ้าน - XY STORE</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
  background: radial-gradient(circle at top, #0f0f0f, #050505 80%);
  color: #fff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

h1 {
  font-size: 2.4rem;
  font-weight: 900;
  text-align: center;
  background: linear-gradient(90deg, #7a3fff, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 40px;
  letter-spacing: 1px;
  text-shadow: 0 0 10px rgba(150, 80, 255, 0.4);
}

.dashboard {
  background: rgba(18, 18, 18, 0.9);
  border: 1px solid rgba(120, 80, 255, 0.3);
  border-radius: 20px;
  box-shadow: 0 0 40px rgba(100, 60, 255, 0.15);
  padding: 50px 60px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
  max-width: 1000px;
  transition: 0.3s;
}

.dashboard:hover {
  box-shadow: 0 0 50px rgba(150, 100, 255, 0.25);
}

.card {
  background: #121212;
  border-radius: 16px;
  padding: 25px 35px;
  text-align: center;
  min-width: 200px;
  transition: 0.3s ease;
  cursor: pointer;
  box-shadow: 0 0 10px rgba(100, 60, 255, 0.15);
  text-decoration: none;
  color: #fff;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 0 20px rgba(150, 100, 255, 0.4);
  background: linear-gradient(145deg, #161616, #1e1e1e);
}

.card i {
  font-size: 1.8rem;
  display: block;
  margin-bottom: 10px;
}

.card span {
  font-weight: 600;
  color: #fff;
  font-size: 1.05rem;
}

.logout {
  margin-top: 30px;
  color: #ff5555;
  text-decoration: none;
  font-weight: 600;
  transition: 0.2s;
  display: inline-block;
}

.logout:hover {
  color: #ff7777;
  text-shadow: 0 0 8px rgba(255, 80, 80, 0.4);
}

/* ปุ่มเรืองแสง */
.card.glow {
  background: linear-gradient(180deg, #1a1a1a, #111);
  border: 1px solid rgba(150, 100, 255, 0.2);
  position: relative;
  overflow: hidden;
}

.card.glow::before {
  content: "";
  position: absolute;
  top: -100%;
  left: -100%;
  width: 300%;
  height: 300%;
  background: conic-gradient(from 0deg, transparent, rgba(150, 100, 255, 0.3), transparent 30%);
  animation: rotate 4s linear infinite;
}

@keyframes rotate {
  to { transform: rotate(360deg); }
}

.card.glow span {
  position: relative;
  z-index: 1;
}
</style>
</head>
<body>

  <h1>ระบบหลังบ้าน <span style="color:#8b5cf6;">XY STORE</span></h1>

  <div class="dashboard">
    <a href="products.php" class="card glow">
      🛍️ <span>จัดการสินค้า</span>
    </a>

    <a href="users.php" class="card glow">
      👥 <span>จัดการผู้ใช้</span>
    </a>

    <a href="topups.php" class="card glow">
      💰 <span>การเติมเงิน</span>
    </a>

    <a href="ip_monitor.php" class="card glow">
      🌐 <span>ตรวจสอบ IP ผู้ใช้</span>
    </a>

    <a href="../index.php" class="card glow">
      🏠 <span>กลับหน้าหลัก</span>
    </a>
  </div>

  <a href="../logout.php" class="logout">🚪 ออกจากระบบ</a>

</body>
</html>
