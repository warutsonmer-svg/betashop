<?php
require_once '../config.php';
require_once '../lib/Auth.php';
Auth::requireAdmin();

// ✅ ฟังก์ชันอัปเดตพอยต์
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $amount = (int)$_POST['amount'];

    if ($_POST['action'] === 'add') {
        $mysqli->query("UPDATE users SET points = points + $amount WHERE id = $user_id");
    } elseif ($_POST['action'] === 'remove') {
        $mysqli->query("UPDATE users SET points = GREATEST(points - $amount, 0) WHERE id = $user_id");
    }

    echo "<script>alert('อัปเดตพอยต์เรียบร้อยแล้ว!');window.location.href='users.php';</script>";
    exit;
}

$users = $mysqli->query("SELECT id, username, discord_id, balance, points, updated_at FROM users ORDER BY id DESC");
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>จัดการผู้ใช้ | XY STORE ADMIN</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  background:#0d0d10;
  color:white;
  font-family:'Prompt',sans-serif;
  margin:0;
}
header {
  background:#1a1a25;
  padding:20px 40px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.container {
  padding:30px;
}
table {
  width:100%;
  border-collapse:collapse;
  margin-top:20px;
}
th, td {
  padding:12px 16px;
  border-bottom:1px solid rgba(255,255,255,0.1);
}
th {
  background:#23233a;
  text-align:left;
}
form {
  display:inline-block;
  margin:0 4px;
}
input[type=number] {
  width:70px;
  background:#1b1b1b;
  border:1px solid #333;
  color:#fff;
  border-radius:6px;
  padding:4px 6px;
}
button {
  background:linear-gradient(90deg,#b37bff,#8b5cf6);
  border:none;
  border-radius:6px;
  color:white;
  padding:6px 10px;
  cursor:pointer;
  font-weight:600;
}
button.remove {
  background:linear-gradient(90deg,#ff5f6d,#ffc371);
}
button:hover {
  opacity:0.85;
}
</style>
</head>
<body>
<header>
  <a href="dashboard.php" style="color:#a0a0ff;text-decoration:none;">← กลับ Dashboard</a>
  <h1>👥 จัดการผู้ใช้</h1>
</header>

<div class="container">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>ชื่อผู้ใช้</th>
        <th>Discord ID</th>
        <th>ยอดเงิน</th>
        <th>แต้ม</th>
        <th>อัปเดตล่าสุด</th>
        <th>จัดการพอยต์</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($u = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['discord_id']) ?></td>
        <td><?= number_format($u['balance'], 2) ?> ฿</td>
        <td><?= $u['points'] ?></td>
        <td><?= $u['updated_at'] ?></td>
        <td>
          <!-- ฟอร์มเพิ่มพอยต์ -->
          <form method="post" style="display:inline;">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <input type="number" name="amount" value="1" min="1">
            <button type="submit" name="action" value="add">+ เพิ่ม</button>
          </form>
          <!-- ฟอร์มลบพอยต์ -->
          <form method="post" style="display:inline;">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <input type="number" name="amount" value="1" min="1">
            <button type="submit" name="action" value="remove" class="remove">− ลบ</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
