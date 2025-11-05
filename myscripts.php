<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$user = $_SESSION['user'];
$discord_id = $mysqli->real_escape_string($user['id']);

// ✅ ดึงพอยต์
$points = 0;
$res = $mysqli->query("SELECT points FROM users WHERE discord_id='{$discord_id}'");
if ($row = $res->fetch_assoc()) $points = (int)$row['points'];

// ✅ ดึงสคริปต์ที่ซื้อแล้ว
$query = "
  SELECT p.id, p.name, p.image, p.details, p.download_url, ph.created_at, ph.ip_address, ph.last_ip_change
  FROM purchases AS ph
  JOIN products AS p ON ph.product_id = p.id
  WHERE ph.discord_id = '{$discord_id}'
  ORDER BY ph.id DESC
";
$res = $mysqli->query($query);
$scripts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$avatar = htmlspecialchars($user['avatar'] ?? 'assets/img/default.png');
$username = htmlspecialchars($user['username'] ?? 'ไม่ทราบชื่อ');
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>สคริปต์ของฉัน - XY STORE</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
  background: #0b0b0b;
  color: #fff;
}

/* ======================= */
/* NAVBAR สไตล์ใหม่        */
/* ======================= */
.topnav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0d0d0d;
  border-radius: 20px;
  padding: 8px 28px;
  width: 85%;
  max-width: 1200px;
  margin: 25px auto;
  box-shadow: 0 0 25px rgba(140, 80, 255, 0.15);
  border: 1px solid rgba(140, 80, 255, 0.25);
}
.brand {
  font-size: 1.3rem;
  font-weight: 800;
  background: linear-gradient(45deg, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.navlinks {
  display: flex;
  align-items: center;
  gap: 26px;
}
.navlinks a {
  color: #ccc;
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.95rem;
  transition: 0.2s;
}
.navlinks a:hover, .navlinks a.active {
  color: #b37bff;
}
.profile-area { display: flex; align-items: center; }
.user-profile {
  display: flex;
  align-items: center;
  background: #1a1a1a;
  padding: 4px 10px;
  border-radius: 16px;
}
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 8px;
}
.username { font-weight: 700; font-size: 0.9rem; }
.points { font-size: 0.8rem; color: #aaa; }

/* ======================= */
/* ตารางสคริปต์ของฉัน     */
/* ======================= */
.container {
  max-width: 1000px;
  margin: 60px auto;
  padding: 0 20px;
}
h1 {
  text-align: center;
  margin-bottom: 30px;
  font-size: 1.8rem;
}

.table {
  width: 100%;
  border-collapse: collapse;
  background: #141414;
  border-radius: 12px;
  overflow: hidden;
}
th, td {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #222;
}
th {
  background: #1e1e2a;
  color: #b37bff;
}
td img {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
  margin-right: 10px;
}
.script-name {
  display: flex;
  align-items: center;
  font-weight: 600;
}
.btn {
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}
.download {
  background: #2563eb;
  color: #fff;
}
.download:hover { background: #1d4ed8; }
.changeip {
  background: #dc2626;
  color: #fff;
}
.changeip:hover { background: #b91c1c; }
.empty {
  text-align: center;
  color: #aaa;
  padding: 60px 0;
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
}
.modal-content {
  background: #1a1a1a;
  color: #fff;
  padding: 25px 30px;
  border-radius: 12px;
  max-width: 400px;
  margin: 10% auto;
  box-shadow: 0 0 20px rgba(150, 80, 255, 0.3);
  text-align: center;
}
.modal-content input {
  width: 90%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #444;
  background: #111;
  color: #fff;
  margin-top: 10px;
  font-size: 15px;
  text-align: center;
}
.modal-content button {
  margin-top: 15px;
  padding: 8px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}
.modal-content .save {
  background: #b37bff;
  color: #fff;
}
.modal-content .cancel {
  background: #333;
  color: #ccc;
}
.timer {
  font-size: 0.85rem;
  color: #ffcc80;
}
</style>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="topnav">
  <div class="brand">XY STORE</div>
  <div class="navlinks">
    <a href="shop.php">🛒 ร้านค้า</a>
    <a href="myscripts.php" class="active">📜 สคริปต์ของฉัน</a>
    <a href="topup.php">💳 เติมเงิน</a>
  </div>
  <div class="profile-area">
    <div class="user-profile">
      <img src="<?= $avatar ?>" class="avatar" alt="avatar">
      <div>
        <div class="username"><?= $username ?></div>
        <div class="points"><?= $points ?> พ้อย</div>
      </div>
    </div>
  </div>
</nav>

<div class="container">
  <h1>สคริปต์ของฉัน</h1>

  <?php if (empty($scripts)): ?>
    <div class="empty">ยังไม่มีสคริปต์ที่ซื้อ</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>สินค้า</th>
          <th>วันที่ซื้อ</th>
          <th>IP</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($scripts as $s):
            $can_change = true;
            $remaining = "";
            if (!empty($s['last_ip_change'])) {
                $last = strtotime($s['last_ip_change']);
                $diff = time() - $last;
                if ($diff < 5 * 3600) {
                    $can_change = false;
                    $remain = 5 * 3600 - $diff;
                    $h = floor($remain / 3600);
                    $m = floor(($remain % 3600) / 60);
                    $remaining = "เหลือเวลาอีก {$h} ชั่วโมง {$m} นาที";
                }
            }
        ?>
        <tr>
          <td class="script-name">
            <img src="uploads/<?= htmlspecialchars($s['image']) ?>" alt="">
            <?= htmlspecialchars($s['name']) ?>
          </td>
          <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($s['created_at']))) ?></td>
          <td id="ip_<?= $s['id'] ?>">
            <?= htmlspecialchars($s['ip_address'] ?? '0.0.0.0') ?><br>
            <?php if(!$can_change): ?><span class="timer">⏳ <?= $remaining ?></span><?php endif; ?>
          </td>
          <td>
            <a href="<?= htmlspecialchars($s['download_url']) ?>" target="_blank">
              <button class="btn download">⬇ ดาวน์โหลด</button>
            </a>
            <?php if($can_change): ?>
              <button class="btn changeip" onclick="openIPModal(<?= $s['id'] ?>)">🔐 เปลี่ยน IP</button>
            <?php else: ?>
              <button class="btn changeip" disabled style="opacity:0.5;cursor:not-allowed;">🔒 รอเวลา</button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Modal -->
<div id="ipModal" class="modal">
  <div class="modal-content">
    <h3>🔐 เปลี่ยน IP สำหรับสคริปต์นี้</h3>
    <input type="text" id="newIpInput" placeholder="กรอก IP ใหม่ เช่น 192.168.1.2">
    <div>
      <button class="save" onclick="saveIP()">บันทึก</button>
      <button class="cancel" onclick="closeIPModal()">ยกเลิก</button>
    </div>
    <p style="font-size:13px;color:#aaa;margin-top:10px;">⏳ หากเปลี่ยนแล้ว ต้องรอ 5 ชั่วโมงก่อนจะเปลี่ยนใหม่ได้</p>
  </div>
</div>

<script>
let currentProductId = null;

function openIPModal(productId) {
  currentProductId = productId;
  document.getElementById('ipModal').style.display = 'block';
}
function closeIPModal() {
  document.getElementById('ipModal').style.display = 'none';
  document.getElementById('newIpInput').value = '';
}
async function saveIP() {
  const newIP = document.getElementById('newIpInput').value.trim();
  if (!newIP) {
    alert("⚠ กรุณากรอก IP ก่อน");
    return;
  }
  const formData = new FormData();
  formData.append("product_id", currentProductId);
  formData.append("new_ip", newIP);

  const res = await fetch("api/update_ip.php", {
    method: "POST",
    body: formData
  });
  const data = await res.json();

  if (data.status === "success") {
    alert("✅ " + data.message);
    setTimeout(() => location.reload(), 500);
  } else {
    alert("❌ " + data.message);
  }
}
</script>
</body>
</html>
