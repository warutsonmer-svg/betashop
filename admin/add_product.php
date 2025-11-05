<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    // 📸 อัปโหลดรูป
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imagePath = "uploads/" . $fileName; // path สำหรับเก็บใน DB

    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

    // ✅ บันทึกข้อมูลลง DB
    $stmt = $mysqli->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $desc, $price, $imagePath);
    $stmt->execute();

    echo "<script>alert('เพิ่มสินค้าสำเร็จ!'); location.href='products.php';</script>";
}
?>
