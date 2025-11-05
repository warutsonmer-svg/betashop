<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>XY STORE</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
  background: #0a0a0a;
  color: #fff;
}

/* ======================= */
/* NAVBAR (ขนาดเล็ก) */
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
  letter-spacing: 0.5px;
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
  transition: 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.95rem;
}

.navlinks a:hover {
  color: #b37bff;
}

.profile-area {
  display: flex;
  align-items: center;
}

.user-menu {
  position: relative;
}

.user-profile {
  display: flex;
  align-items: center;
  background: #1a1a1a;
  padding: 4px 10px;
  border-radius: 16px;
  cursor: pointer;
  transition: background 0.3s;
}

.user-profile:hover {
  background: #2a2a2a;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 8px;
}

.profile-text {
  line-height: 1.1;
}

.username {
  font-weight: 700;
  font-size: 0.9rem;
}

.points {
  font-size: 0.8rem;
  color: #aaa;
}

/* Dropdown */
.dropdown-menu {
  display: none;
  position: absolute;
  right: 0;
  top: 52px;
  background: #111;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.4);
  width: 180px;
  z-index: 1000;
}

.dropdown-menu a {
  display: block;
  padding: 10px 14px;
  color: #ddd;
  text-decoration: none;
  font-weight: 500;
  transition: 0.2s;
}

.dropdown-menu a:hover {
  background: #1e1e1e;
  color: #b37bff;
}

.dropdown-menu .logout {
  color: #ff4d4d;
}
.dropdown-menu .logout:hover {
  background: #2a0000;
}

/* ======================= */
/* HERO SECTION */
/* ======================= */
.hero {
  position: relative;
  z-index: 1;
  background: radial-gradient(circle at top center, #151515 0%, #0a0a0a 90%);
  height: 65vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  margin-bottom: -60px;
}

.hero-card {
  background: rgba(15, 15, 15, 0.85);
  padding: 60px 80px;
  border-radius: 25px;
  box-shadow: 0 0 40px rgba(140, 80, 255, 0.25);
  backdrop-filter: blur(8px);
}
.hero-card h1 {
  font-size: 2.2rem;
  font-weight: 900;
  margin-bottom: 14px;
}
.accent {
  background: linear-gradient(90deg, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.lead {
  font-size: 1.05rem;
  color: #ccc;
  margin-bottom: 24px;
}
.cta {
  background: #8b5cf6;
  color: #fff;
  padding: 12px 26px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 700;
  transition: 0.25s;
}
.cta:hover { opacity: 0.85; box-shadow: 0 0 15px rgba(150, 100, 255, 0.3); }

/* ======================= */
/* FEATURES */
/* ======================= */
.features {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 24px;
  padding: 60px 20px;
  background: #0d0d0d;
}

.feature {
  background: rgba(20, 20, 20, 0.75);
  border-radius: 16px;
  padding: 40px 30px;
  text-align: center;
  width: 300px;
  border: 1px solid #2a2a2a;
  box-shadow: 0 0 25px rgba(180, 120, 255, 0.08);
  transition: 0.3s;
}
.feature:hover {
  transform: translateY(-6px);
  box-shadow: 0 0 35px rgba(180, 120, 255, 0.25);
}
.icon {
  font-size: 2.2rem;
  margin-bottom: 14px;
}
.feature h3 {
  color: #b37bff;
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 8px;
}
.feature p {
  color: #aaa;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* ======================= */
/* FOOTER */
/* ======================= */
.footer {
  text-align: center;
  padding: 20px;
  background: #0a0a0a;
  color: #777;
  font-size: 0.9rem;
  border-top: 1px solid #1a1a1a;
}
</style>
</head>
<body>

  <nav class="topnav">
    <div class="brand">XY STORE</div>

    <div class="navlinks">
      <a href="shop.php">🛒 ร้านค้า</a>
      <a href="myscripts.php">📜 สคริปต์ของฉัน</a>
      <a href="topup.php">💳 เติมเงิน</a>
    </div>

    <div class="profile-area">
    <?php if (isset($_SESSION['user'])):
        $u = $_SESSION['user'];
        $sid = $mysqli->real_escape_string($u['id']);
        $res = $mysqli->query("SELECT points FROM users WHERE discord_id='{$sid}'");
        $points = 0;
        if ($row = $res->fetch_assoc()) $points = (int)$row['points'];
        $avatar = htmlspecialchars($u['avatar'] ?? 'assets/img/default.png');
    ?>
      <div class="user-menu">
        <div class="user-profile" onclick="toggleMenu()">
          <img src="<?= $avatar ?>" alt="avatar" class="avatar">
          <div class="profile-text">
            <div class="username"><?= htmlspecialchars($u['username']) ?></div>
            <div class="points"><?= htmlspecialchars($points) ?> พ้อย</div>
          </div>
        </div>

        <div class="dropdown-menu" id="dropdownMenu">
          <?php if (isset($admin_ids) && in_array($sid, $admin_ids)): ?>
            <a href="admin/dashboard.php">⚙️ หลังบ้าน</a>
          <?php endif; ?>
          <a href="logout.php" class="logout">🚪 ออกจากระบบ</a>
        </div>
      </div>
    <?php else: ?>
      <a href="login.php" class="login-btn">Login with Discord</a>
    <?php endif; ?>
    </div>
  </nav>

  <!-- ✅ HERO SECTION -->
  <header class="hero">
    <div class="hero-card">
      <h1>ยินดีต้อนรับสู่ <span class="accent">XY STORE</span></h1>
      <p class="lead">CMD และโปแกรมช่วยเพิ่มประสิทธิภาพสำหรับเกม FIVEM</p>
      <a href="shop.php" class="cta">เริ่มช้อปปิ้ง</a>
    </div>
  </header>

  <!-- ✅ FEATURES -->
  <section class="features">
    <div class="feature">
      <div class="icon">🛡️</div>
      <h3>สินค้าคุณภาพ</h3>
      <p>สินค้าทุกชิ้นผ่านการคัดสรรอย่างดีและทดสอบแล้ว</p>
    </div>

    <div class="feature">
      <div class="icon">💰</div>
      <h3>ระบบพอยต์</h3>
      <p>เติมเงินเพื่อแลกพอยต์และซื้อสินค้าได้ทันที</p>
    </div>

    <div class="feature">
      <div class="icon">⏱️</div>
      <h3>บริการ 24 ชม.</h3>
      <p>พร้อมให้บริการและดาวน์โหลดตลอด 24 ชั่วโมง</p>
    </div>
  </section>

  <footer class="footer">
    &copy; <?= date('Y') ?> XY STORE
  </footer>

<script>
function toggleMenu() {
  const menu = document.getElementById('dropdownMenu');
  menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}
document.addEventListener('click', function(e) {
  const menu = document.getElementById('dropdownMenu');
  const profile = document.querySelector('.user-profile');
  if (menu && profile && !profile.contains(e.target)) {
    menu.style.display = 'none';
  }
});
</script>

</body>
</html>
