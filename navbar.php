<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
?>
<nav class="topnav">
  <div class="brand">XY STORE</div>

  <div class="navlinks">
    <a href="shop.php">ร้านค้า</a>
    <a href="myscripts.php">สคริปต์ของฉัน</a>
    <a href="topup.php">เติมเงิน</a>
  </div>

  <div class="profile-area">
<?php if (isset($_SESSION['user'])):
    $u = $_SESSION['user'];
    $sid = $mysqli->real_escape_string($u['id']);
    $res = $mysqli->query("SELECT points FROM users WHERE discord_id='{$sid}'");
    $points = 0;
    if ($row = $res->fetch_assoc()) $points = (int)$row['points'];

    // ✅ รวมข้อมูล user กับแต้มเข้าด้วยกัน
    $user = array_merge($u, ['points' => $points]);

    $avatar = htmlspecialchars($user['avatar']);
?>
    <div class="user-menu">
      <div class="user-profile" onclick="toggleMenu()">
        <img src="<?= $avatar ?>" alt="avatar" class="avatar">
        <div class="profile-text">
          <div class="username"><?= htmlspecialchars($user['username']) ?></div>
          <span id="userPoints"><?= htmlspecialchars($user['points']) ?> พ้อย</span>
        </div>
      </div>

      <div class="dropdown-menu" id="dropdownMenu">
        <!-- <a href="purchase_history.php"><i class="fa fa-file-text-o"></i> ประวัติการซื้อ</a> -->

        <?php if (in_array($sid, $admin_ids)): ?>
            <a href="admin/dashboard.php"><i class="fa fa-cog"></i> หลังบ้าน</a>
        <?php endif; ?>

        <a href="logout.php" class="logout"><i class="fa fa-sign-out"></i> ออกจากระบบ</a>
      </div>
    </div>
<?php else: ?>
    <a href="login.php" class="login-btn">Login with Discord</a>
<?php endif; ?>
  </div>
</nav>

<style>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap');

body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
}

.topnav {
  background: linear-gradient(90deg, #0b0b0b, #151515);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 40px;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.brand {
  font-size: 1.6rem;
  font-weight: 800;
  background: linear-gradient(45deg, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.navlinks a {
  color: #ccc;
  text-decoration: none;
  margin: 0 12px;
  font-weight: 500;
  transition: 0.2s;
}

.navlinks a:hover {
  color: #b37bff;
}

.profile-area {
  display: flex;
  align-items: center;
}

.login-btn {
  background: #b37bff;
  padding: 8px 16px;
  border-radius: 12px;
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s;
}

.login-btn:hover {
  background: #9c66ff;
}

/* ======================= */
/*     User Profile Menu   */
/* ======================= */
.user-menu {
  position: relative;
  display: inline-block;
}

.user-profile {
  display: flex;
  align-items: center;
  background: #1a1a1a;
  padding: 6px 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.3s;
}

.user-profile:hover {
  background: #2a2a2a;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  margin-right: 10px;
}

.profile-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.username {
  font-weight: 700;
  font-size: 0.95rem;
  color: #fff;
}

.points {
  font-size: 0.8rem;
  color: #bbb;
}

/* ======================= */
/*      Dropdown Menu      */
/* ======================= */
.dropdown-menu {
  display: none;
  position: absolute;
  right: 0;
  top: 60px;
  background: #0f0f0f;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.6);
  overflow: hidden;
  width: 200px;
  z-index: 999;
  animation: fadeIn 0.2s ease;
}

.dropdown-menu a {
  display: block;
  padding: 12px 16px;
  color: #ddd;
  text-decoration: none;
  font-weight: 500;
  transition: background 0.2s, color 0.2s;
}

.dropdown-menu a:hover {
  background: #1d1d1d;
  color: #fff;
}

.dropdown-menu .logout {
  color: #ff4d4d;
}

.dropdown-menu .logout:hover {
  background: #2a0000;
}

/* ======================= */
/*      Animations         */
/* ======================= */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function toggleMenu() {
  const menu = document.getElementById('dropdownMenu');
  menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
  const menu = document.getElementById('dropdownMenu');
  const profile = document.querySelector('.user-profile');
  if (menu && profile && !profile.contains(e.target)) {
    menu.style.display = 'none';
  }
});
</script>
