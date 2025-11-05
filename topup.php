<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$discord_id = $mysqli->real_escape_string($user['id']);
$username = $mysqli->real_escape_string($user['username']);

// ✅ ฟังก์ชันส่ง Discord Webhook
function sendDiscordWebhook($username, $amount, $note, $slipUrl) {
    if (!defined('DISCORD_WEBHOOK_TOPUP') || empty(DISCORD_WEBHOOK_TOPUP)) return;

    $data = [
        "username" => "💸 ระบบเติมเงิน | XY STORE",
        "avatar_url" => "https://cdn-icons-png.flaticon.com/512/4401/4401414.png",
        "embeds" => [[
            "title" => "📥 มีคำขอเติมเงินใหม่",
            "color" => 11796287, // 💜 สี embed
            "fields" => [
                ["name" => "👤 ผู้ใช้", "value" => $username, "inline" => true],
                ["name" => "💰 จำนวนเงิน", "value" => "{$amount} บาท", "inline" => true],
                ["name" => "📝 หมายเหตุ", "value" => $note ? $note : "-", "inline" => false],
            ],
            "image" => ["url" => $slipUrl],
            "footer" => [
                "text" => "ระบบแจ้งเตือนจาก XY STORE",
                "icon_url" => "https://cdn-icons-png.flaticon.com/512/5968/5968756.png"
            ],
            "timestamp" => date("c"),
        ]]
    ];

    $ch = curl_init(DISCORD_WEBHOOK_TOPUP);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// ✅ ถ้ามีการส่งฟอร์ม
if (isset($_POST['submit_topup'])) {
    $amount = (int)$_POST['amount'];
    $note = $mysqli->real_escape_string($_POST['note']);

    // ✅ อัปโหลดสลิป
    $slipPath = '';
    $slipUrl = '';
    if (!empty($_FILES['slip']['name'])) {
        $uploadDir = "uploads/slips/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = uniqid("slip_") . "_" . basename($_FILES['slip']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['slip']['tmp_name'], $targetFile)) {
            $slipPath = $targetFile;
            $slipUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/" . $slipPath;
        }
    }

    // ✅ บันทึกคำขอเติมเงิน
    $sql = "INSERT INTO topup_requests (discord_id, username, amount, note, slip, status)
            VALUES ('$discord_id', '$username', '$amount', '$note', '$slipPath', 'pending')";
    if ($mysqli->query($sql)) {
        // ✅ ส่งแจ้งเตือน Discord
        sendDiscordWebhook($username, $amount, $note, $slipUrl);
        echo "<script>alert('✅ ส่งคำขอเติมเงินเรียบร้อยแล้ว! กรุณารอแอดมินตรวจสอบ');</script>";
    } else {
        echo "<script>alert('❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล');</script>";
    }
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>XY STORE | เติมเงิน</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700;900&display=swap" rel="stylesheet">
<style>
body {
  background: #0b0b0b;
  color: #fff;
  font-family: 'Prompt', sans-serif;
  margin: 0;
}

/* ✅ NAVBAR แบบใหม่ */
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

/* ✅ เนื้อหา */
.container {
  max-width: 1100px;
  margin: auto;
  padding: 40px 20px;
}
h1 {
  text-align: center;
  font-weight: 900;
  margin-bottom: 40px;
  color: #fff;
  letter-spacing: 1px;
}

.topup-wrapper {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}
.card {
  background: #111;
  border-radius: 20px;
  box-shadow: 0 0 10px rgba(255,255,255,0.05);
  padding: 24px;
}
h2 {
  font-size: 1.2rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
}
input, textarea {
  width: 100%;
  background: #1b1b1b;
  border: 1px solid #2f2f2f;
  border-radius: 10px;
  padding: 12px 14px;
  color: #fff;
  margin-bottom: 14px;
}
input:focus, textarea:focus {
  outline: none;
  border-color: #b37bff;
}
button {
  background: linear-gradient(90deg, #b37bff, #8b5cf6);
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  width: 100%;
}
button:hover { opacity: 0.85; }
.note {
  font-size: 0.85rem;
  color: #aaa;
}
.history-box table {
  width: 100%;
  border-collapse: collapse;
}
.history-box th, .history-box td {
  padding: 10px 8px;
  border-bottom: 1px solid #222;
}
.history-box th {
  color: #b37bff;
  text-align: left;
}
@media(max-width: 900px) {
  .topup-wrapper { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- ✅ Navbar -->
<?php
$avatar = htmlspecialchars($user['avatar'] ?? 'assets/img/default.png');
$usernameDisplay = htmlspecialchars($user['username'] ?? 'ไม่ทราบชื่อ');
$points = 0;
$res = $mysqli->query("SELECT points FROM users WHERE discord_id='{$discord_id}'");
if ($row = $res->fetch_assoc()) $points = (int)$row['points'];
?>
<nav class="topnav">
  <div class="brand">XY STORE</div>
  <div class="navlinks">
    <a href="shop.php">🛒 ร้านค้า</a>
    <a href="myscripts.php">📜 สคริปต์ของฉัน</a>
    <a href="topup.php" class="active">💳 เติมเงิน</a>
  </div>
  <div class="profile-area">
    <div class="user-profile">
      <img src="<?= $avatar ?>" class="avatar" alt="avatar">
      <div>
        <div class="username"><?= $usernameDisplay ?></div>
        <div class="points"><?= $points ?> พ้อย</div>
      </div>
    </div>
  </div>
</nav>

<!-- ✅ เนื้อหา -->
<div class="container">
  <h1>เติมพอยต์เพื่อซื้อสินค้า</h1>
  <div class="topup-wrapper">
    <!-- ✅ ฝั่งซ้าย -->
    <div class="card">
      <h2>🏦 แจ้งเติมเงิน</h2>
      <p>บัญชีธนาคารสำหรับโอนเงิน</p>
      <div style="background:#1b1b1b; border:1px solid #2f2f2f; border-radius:10px; padding:12px; margin-bottom:16px;">
        <b>ธนาคารกรุงศรี</b><br>
        8041057742 (ชื่อบัญชี: <b>นภัทศยา ปุยผา</b>)
      </div>

      <form method="post" enctype="multipart/form-data">
        <label>จำนวนเงิน (บาท)</label>
        <input type="number" name="amount" placeholder="ระบุจำนวนเงินที่โอน" required>
        <p class="note">1 บาท = 1 พอยต์</p>

        <label>หมายเหตุเพิ่มเติม (ถ้ามี)</label>
        <textarea name="note" rows="3" placeholder="หมายเหตุเพิ่มเติม..."></textarea>

        <label>สลิปการโอนเงิน</label>
        <input type="file" name="slip" accept="image/*" required style="margin-bottom:14px;">

        <button type="submit" name="submit_topup">ส่งคำขอเติมเงิน</button>
      </form>
    </div>

    <!-- ✅ ฝั่งขวา -->
    <div class="card">
      <h2>🕓 ประวัติการเติมเงิน</h2>
      <div class="history-box">
        <?php
        $res = $mysqli->query("SELECT * FROM topup_requests WHERE discord_id='{$discord_id}' ORDER BY id DESC");
        if ($res && $res->num_rows > 0):
          echo "<table>";
          echo "<tr><th>จำนวน</th><th>สถานะ</th><th>ดูสลิป</th></tr>";
          while($r = $res->fetch_assoc()):
              echo "<tr>";
              echo "<td>{$r['amount']} บาท</td>";
              echo "<td>";
              if ($r['status'] == 'pending') echo "<span style='color:orange;'>รอตรวจสอบ</span>";
              elseif ($r['status'] == 'approved') echo "<span style='color:lightgreen;'>อนุมัติแล้ว</span>";
              else echo "<span style='color:red;'>ปฏิเสธ</span>";
              echo "</td>";
              echo "<td>";
              if ($r['slip']) echo "<a href='{$r['slip']}' target='_blank' style='color:#b37bff;'>เปิด</a>";
              else echo "-";
              echo "</td>";
              echo "</tr>";
          endwhile;
          echo "</table>";
        else:
          echo "ยังไม่มีประวัติการเติมเงิน";
        endif;
        ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
