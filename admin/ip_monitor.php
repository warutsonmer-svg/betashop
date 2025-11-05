<?php
require '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['id'], $admin_ids)) {
  header("Location: ../index.php");
  exit;
}

// ✅ ดึงข้อมูล IP ทั้งหมด
$query = "
  SELECT ph.id AS purchase_id, ph.discord_id, u.username, p.name AS product_name,
         ph.ip_address, ph.last_ip_change, ph.created_at
  FROM purchases AS ph
  LEFT JOIN users AS u ON u.discord_id = ph.discord_id
  LEFT JOIN products AS p ON ph.product_id = p.id
  ORDER BY ph.id DESC
";
$res = $mysqli->query($query);
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ตรวจสอบ IP ผู้ใช้ - XY STORE</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
  background: #0c0c0c;
  color: #fff;
  padding: 30px;
}
h1 {
  text-align: center;
  font-weight: 800;
  margin-bottom: 30px;
  background: linear-gradient(90deg, #8b5cf6, #b37bff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: #141414;
  border-radius: 12px;
  overflow: hidden;
}
th, td {
  padding: 10px 12px;
  border-bottom: 1px solid #222;
  text-align: left;
  font-size: 14px;
}
th {
  background: #1e1e2a;
  color: #b37bff;
}
td { color: #ddd; }
tr:hover { background: #1c1c1c; }

.btn {
  background: #2a2a2a;
  border: none;
  border-radius: 6px;
  color: #fff;
  padding: 6px 10px;
  cursor: pointer;
  margin: 2px;
  font-size: 13px;
  transition: 0.2s;
}
.btn:hover { opacity: 0.8; }
.reset { background: #3b82f6; }
.add { background: #16a34a; }
.del { background: #ef4444; }
.ban { background: #9333ea; }

a.back {
  color: #b37bff;
  text-decoration: none;
  display: inline-block;
  margin-top: 20px;
}
a.back:hover { text-decoration: underline; }

input.ip-input {
  background: #111;
  color: #fff;
  border: 1px solid #333;
  border-radius: 5px;
  padding: 5px;
  width: 130px;
  text-align: center;
}
</style>
</head>
<body>
  <h1>🌐 ตรวจสอบ / จัดการ IP ผู้ใช้</h1>

  <?php if ($res && $res->num_rows > 0): ?>
  <table>
    <tr>
      <th>ชื่อผู้ใช้</th>
      <th>Discord ID</th>
      <th>สินค้า</th>
      <th>IP ล่าสุด</th>
      <th>จัดการ</th>
      <th>เปลี่ยน IP ล่าสุด</th>
      <th>วันที่ซื้อ</th>
    </tr>
    <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['username'] ?? '-') ?></td>
      <td><?= htmlspecialchars($r['discord_id']) ?></td>
      <td><?= htmlspecialchars($r['product_name']) ?></td>
      <td id="ip_<?= $r['purchase_id'] ?>"><?= htmlspecialchars($r['ip_address'] ?? '-') ?></td>
      <td>
        <button class="btn reset" onclick="adminAction('reset', <?= $r['purchase_id'] ?>)">รีเซ็ต</button>
        <input type="text" id="newip_<?= $r['purchase_id'] ?>" class="ip-input" placeholder="เพิ่ม IP">
        <button class="btn add" onclick="adminAction('add', <?= $r['purchase_id'] ?>)">เพิ่ม</button>
        <button class="btn del" onclick="adminAction('delete', <?= $r['purchase_id'] ?>)">ลบ</button>
        <button class="btn ban" onclick="adminAction('ban', <?= $r['purchase_id'] ?>)">แบน</button>
      </td>
      <td><?= $r['last_ip_change'] ? htmlspecialchars(date('Y-m-d H:i:s', strtotime($r['last_ip_change']))) : '-' ?></td>
      <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($r['created_at']))) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center;color:#aaa;">ไม่มีข้อมูล IP</p>
  <?php endif; ?>

  <div style="text-align:center;">
    <a href="dashboard.php" class="back">⬅ กลับหน้าแดชบอร์ด</a>
  </div>

<script>
async function adminAction(action, id) {
  let data = { action, id };
  if (action === 'add') {
    const newIp = document.getElementById(`newip_${id}`).value.trim();
    if (!newIp) { alert("⚠ กรุณากรอก IP ที่จะเพิ่ม"); return; }
    data.new_ip = newIp;
  }
  if (!confirm("ยืนยันการทำรายการนี้?")) return;

  const res = await fetch('admin_ip_action.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  const json = await res.json();
  alert(json.msg);
  if (json.status === 'success') location.reload();
}
</script>
</body>
</html>
