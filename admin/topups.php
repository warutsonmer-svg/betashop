<?php
require '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// ถ้ามีการกด "อนุมัติ" หรือ "ปฏิเสธ"
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];

    // ดึงข้อมูลคำขอ
    $req = $mysqli->query("SELECT * FROM topup_requests WHERE id=$id");
    if ($req && $req->num_rows > 0) {
        $r = $req->fetch_assoc();
        $discord_id = $mysqli->real_escape_string($r['discord_id']);
        $amount = (int)$r['amount'];

        if ($action === 'approve') {
            // อัปเดตสถานะคำขอ
            $mysqli->query("UPDATE topup_requests SET status='approved' WHERE id=$id");

            // เพิ่มพอยต์ให้ผู้ใช้
            $mysqli->query("UPDATE users SET points = points + $amount WHERE discord_id='$discord_id'");
        } elseif ($action === 'reject') {
            // อัปเดตสถานะเป็นปฏิเสธ
            $mysqli->query("UPDATE topup_requests SET status='rejected' WHERE id=$id");
        }
    }
    header("Location: topups.php");
    exit;
}

?>

<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>จัดการคำขอเติมเงิน</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;800&display=swap" rel="stylesheet">
<style>
body {
  background: #0c0c0c;
  color: #fff;
  font-family: 'Prompt', sans-serif;
  margin: 0;
}
.container {
  max-width: 1200px;
  margin: auto;
  padding: 40px 20px;
}
h1 {
  color: #fff;
  font-weight: 800;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
a {
  color: #b37bff;
  text-decoration: none;
  font-size: 1.2rem;
}
a:hover { text-decoration: underline; }
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th, td {
  padding: 14px;
  border-bottom: 1px solid #222;
  text-align: left;
}
th {
  background: #1a1a2b;
}
td {
  background: #111;
}
.status-pending { color: orange; }
.status-approved { color: lightgreen; }
.status-rejected { color: red; }
button {
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
}
.btn-approve { background: #4ade80; } /* เขียว */
.btn-reject { background: #ef4444; }  /* แดง */
.btn-view { background: #8b5cf6; }   /* ม่วง */
button:hover { opacity: 0.85; }
</style>
</head>
<body>

<div class="container">
  <h1>
    <a href="dashboard.php">← กลับ Dashboard</a>
    💰 การเติมเงิน
  </h1>

  <table>
    <tr>
      <th>ID</th>
      <th>ผู้ใช้</th>
      <th>จำนวน</th>
      <th>สถานะ</th>
      <th>สลิป</th>
      <th>จัดการ</th>
    </tr>

    <?php
    $res = $mysqli->query("SELECT * FROM topup_requests ORDER BY id DESC");
    if ($res && $res->num_rows > 0):
      while($r = $res->fetch_assoc()):
    ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['username']) ?></td>
        <td><?= $r['amount'] ?> บาท</td>
        <td>
          <?php if ($r['status'] == 'pending'): ?>
            <span class="status-pending">รอตรวจสอบ</span>
          <?php elseif ($r['status'] == 'approved'): ?>
            <span class="status-approved">อนุมัติแล้ว</span>
          <?php else: ?>
            <span class="status-rejected">ปฏิเสธ</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!empty($r['slip'])): ?>
            <a href="../<?= $r['slip'] ?>" target="_blank">
              <button class="btn-view">ดูสลิป</button>
            </a>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($r['status'] == 'pending'): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button type="submit" name="action" value="approve" class="btn-approve">อนุมัติ</button>
            </form>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button type="submit" name="action" value="reject" class="btn-reject">ปฏิเสธ</button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="6" style="text-align:center;">ไม่มีคำขอเติมเงิน</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
