<?php 
require '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ ป้องกันคนที่ไม่ใช่แอดมินเข้า
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['id'], $admin_ids)) {
    header("Location: ../index.php");
    exit;
}

// ✅ เพิ่มสินค้า
if (isset($_POST['add'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $price = (int)$_POST['price'];
    $details = $mysqli->real_escape_string($_POST['details']);
    $download_url = $mysqli->real_escape_string($_POST['download_url']);
    $stock = (int)$_POST['stock']; // ✅ เพิ่มจำนวนสต็อก

    // ✅ อัปโหลดรูป
    $imgName = '';
    if (!empty($_FILES['image']['name'])) {
        $imgName = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $imgName);
    }

    // ✅ บันทึกลงฐานข้อมูล (เพิ่ม stock ด้วย)
    $sql = "INSERT INTO products (name, price, details, download_url, image, stock)
            VALUES ('$name', '$price', '$details', '$download_url', '$imgName', '$stock')";
    $mysqli->query($sql);
}

// ✅ ลบสินค้า
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $mysqli->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit;
}

// ✅ ดึงสินค้าทั้งหมด
$result = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>จัดการสินค้า - XY STORE</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700;900&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Prompt', sans-serif;
  background: #0a0a0a;
  color: #fff;
}
a { text-decoration: none; }

.topbar {
  background: #111;
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.back-link {
  color: #b37bff;
  font-weight: 600;
  display: flex;
  align-items: center;
}
.back-link:hover { text-decoration: underline; }

.container {
  padding: 30px 50px;
}
form.add-form {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}
.add-form input {
  background: #181818;
  border: none;
  padding: 10px 15px;
  color: #fff;
  border-radius: 10px;
  outline: none;
}
.add-btn {
  background: #8b5cf6;
  border: none;
  color: #fff;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.3s;
}
.add-btn:hover { background: #a78bfa; }

table {
  width: 100%;
  border-collapse: collapse;
  background: #0e0e0e;
  border-radius: 12px;
  overflow: hidden;
}
th, td {
  padding: 12px 16px;
  text-align: left;
}
th {
  background: #141414;
  color: #b37bff;
  font-weight: 600;
}
tr:nth-child(even) {
  background: #101010;
}
img.thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}
.btn-del {
  background: #ff4d4d;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
.btn-del:hover { background: #cc0000; }
</style>
</head>
<body>
  <div class="topbar">
    <a href="dashboard.php" class="back-link">← กลับ Dashboard</a>
    <div style="font-weight:700; font-size:1.2rem;">🛒 จัดการสินค้า</div>
  </div>

  <div class="container">
    <form method="post" enctype="multipart/form-data" class="add-form">
      <input type="text" name="name" placeholder="ชื่อสินค้า" required>
      <input type="number" name="price" placeholder="ราคา" required>
      <input type="text" name="details" placeholder="รายละเอียด" required>
      <input type="number" name="stock" placeholder="จำนวนสต็อก" required> <!-- ✅ เพิ่มช่องกรอกสต็อก -->
      <input type="text" name="download_url" placeholder="ลิงก์ดาวน์โหลด (เช่น https://...)" required>
      <input type="file" name="image" accept="image/*" required>
      <button type="submit" name="add" class="add-btn">➕ เพิ่มสินค้า</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>รูป</th>
          <th>ชื่อสินค้า</th>
          <th>ราคา</th>
          <th>รายละเอียด</th>
          <th>สต็อก</th> <!-- ✅ เพิ่มคอลัมน์สต็อก -->
          <th>ลิงก์ดาวน์โหลด</th>
          <th>ลบ</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><img src="../uploads/<?= htmlspecialchars($row['image']) ?>" class="thumb"></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['price']) ?></td>
          <td><?= htmlspecialchars($row['details']) ?></td>
          <td><?= htmlspecialchars($row['stock']) ?></td> <!-- ✅ แสดงสต็อก -->
          <td><a href="<?= htmlspecialchars($row['download_url']) ?>" target="_blank" style="color:#8b5cf6;">เปิดลิงก์</a></td>
          <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('ยืนยันการลบ?')"><button class="btn-del">ลบ</button></a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
