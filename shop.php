<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ ถ้าไม่ได้ล็อกอินด้วย Discord ให้เด้งไปหน้า login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// ✅ ดึงข้อมูลสินค้า
$products = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>XY STORE | ร้านค้า</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700;900&display=swap" rel="stylesheet">
<style>
body {
  background: #0b0b0b;
  color: #fff;
  font-family: 'Prompt', sans-serif;
  margin: 0;
}

/* ======================= */
/* NAVBAR เหมือนหน้าแรก */
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
.profile-area { display: flex; align-items: center; }
.user-menu { position: relative; }
.user-profile {
  display: flex;
  align-items: center;
  background: #1a1a1a;
  padding: 4px 10px;
  border-radius: 16px;
  cursor: pointer;
  transition: background 0.3s;
}
.user-profile:hover { background: #2a2a2a; }
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 8px;
}
.profile-text { line-height: 1.1; }
.username { font-weight: 700; font-size: 0.9rem; }
.points { font-size: 0.8rem; color: #aaa; }
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
.dropdown-menu a:hover { background: #1e1e1e; color: #b37bff; }
.dropdown-menu .logout { color: #ff4d4d; }
.dropdown-menu .logout:hover { background: #2a0000; }

/* ======================= */
/* สินค้า */
/* ======================= */
.container {
  max-width: 1100px;
  margin: auto;
  padding: 40px 20px;
}
.header-title {
  font-size: 2.2rem;
  font-weight: 900;
  text-align: center;
  margin-bottom: 0.5rem;
}
.subtitle {
  text-align: center;
  color: #aaa;
  margin-bottom: 30px;
}
.search-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}
.search-bar input {
  width: 60%;
  padding: 12px 20px;
  border-radius: 12px;
  border: none;
  background: #181818;
  color: #fff;
  font-size: 1rem;
}
.search-bar input::placeholder { color: #777; }
.category-select {
  background: #181818;
  border: none;
  color: #fff;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 1rem;
  cursor: pointer;
  appearance: none;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' fill='white' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 1em;
}
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
}
.product-card {
  background-color: #111;
  border-radius: 16px;
  padding: 12px;
  transition: 0.2s;
}
.product-card:hover {
  transform: translateY(-5px);
  background: #1a1a1a;
}
.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;
}
.product-info { padding: 14px; }
.product-title {
  font-weight: 700;
  font-size: 1.05rem;
  margin-bottom: 6px;
  color: #fff;
}
.product-desc {
  font-size: 0.85rem;
  color: #bbb;
  margin-bottom: 12px;
  height: 36px;
  overflow: hidden;
}
.price {
  font-weight: 700;
  color: #b37bff;
  font-size: 1rem;
}
.buy-btn {
  background: #b37bff;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  float: right;
  font-size: 0.9rem;
  transition: 0.2s;
}
.buy-btn:hover { background: #9c66ff; }
</style>
</head>
<body>

<!-- ✅ NAVBAR -->
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
          <div class="points" id="userPoints"><?= htmlspecialchars($points) ?> พ้อย</div>
        </div>
      </div>
      <div class="dropdown-menu" id="dropdownMenu">
        <?php if (isset($admin_ids) && in_array($sid, $admin_ids)): ?>
          <a href="admin/dashboard.php">⚙️ หลังบ้าน</a>
        <?php endif; ?>
        <a href="logout.php" class="logout">🚪 ออกจากระบบ</a>
      </div>
    </div>
  <?php endif; ?>
  </div>
</nav>

<div class="container">
  <h1 class="header-title">ร้านค้า</h1>
  <p class="subtitle">เลือกซื้อสินค้าที่คุณต้องการ</p>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="🔍 ค้นหาสินค้า...">
    <select class="category-select" id="categorySelect">
      <option value="all">🛍️ ทุกหมวดหมู่</option>
      <option value="script">📜 สคริปต์</option>
      <option value="map">🗺️ แผนที่</option>
      <option value="vehicle">🚗 ยานพาหนะ</option>
      <option value="eup">👕 EUP</option>
    </select>
  </div>

  <div class="product-grid" id="productGrid">
    <?php while ($p = $products->fetch_assoc()): ?>
      <div class="product-card" data-category="<?= htmlspecialchars($p['category'] ?? 'other') ?>">
        <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="">
        <div class="product-info">
          <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
          <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
          <div class="price"><?= (int)$p['price'] ?> พ้อย</div>
          <button class="buy-btn" onclick="buyProduct(<?= $p['id'] ?>)">ซื้อ</button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

// ✅ ฟังก์ชันซื้อสินค้า
function buyProduct(productId) {
  Swal.fire({
    title: 'ยืนยันการซื้อ?',
    text: "ต้องการซื้อสินค้านี้หรือไม่",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#8a63f8',
    cancelButtonColor: '#333',
    confirmButtonText: 'ซื้อเลย',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('buy.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire('สำเร็จ!', data.message, 'success');
          const pointsDisplay = document.querySelector('#userPoints');
          if (pointsDisplay) pointsDisplay.textContent = data.points + ' พ้อย';
        } else {
          Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error'));
    }
  });
}

// ✅ ฟังก์ชันค้นหา
document.querySelector('#searchInput').addEventListener('input', function() {
  const keyword = this.value.toLowerCase();
  document.querySelectorAll('.product-card').forEach(card => {
    const title = card.querySelector('.product-title').textContent.toLowerCase();
    card.style.display = title.includes(keyword) ? '' : 'none';
  });
});

// ✅ ฟังก์ชันกรองหมวดหมู่
document.querySelector('#categorySelect').addEventListener('change', function() {
  const selected = this.value;
  document.querySelectorAll('.product-card').forEach(card => {
    const cat = card.getAttribute('data-category');
    card.style.display = (selected === 'all' || cat === selected) ? '' : 'none';
  });
});
</script>
</body>
</html>
