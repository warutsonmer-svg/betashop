<?php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบ session user
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อนซื้อ']);
    exit;
}

// ดึง discord id จาก session (session ที่ได้มาจาก Discord callback ของคุณ)
$discord_id = $_SESSION['user']['id'] ?? $_SESSION['user']['discord_id'] ?? null;
if (!$discord_id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลผู้ใช้ใน session']);
    exit;
}

// รับ product_id
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบสินค้าที่ต้องการซื้อ']);
    exit;
}

// BEGIN transaction
$mysqli->begin_transaction();

try {
    // ดึงข้อมูลผู้ใช้ -> ใช้คอลัมน์ points (int)
    $stmt = $mysqli->prepare("SELECT id, discord_id, points FROM users WHERE discord_id = ?");
    if (!$stmt) throw new Exception("Prepare users failed: " . $mysqli->error);
    $stmt->bind_param("s", $discord_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception("ไม่พบข้อมูลผู้ใช้");
    }

    // ดึงข้อมูลสินค้า (ล็อกแถวด้วย FOR UPDATE เพื่อป้องกัน race condition)
    // MySQLi ไม่รองรับ SELECT ... FOR UPDATE ถ้าไม่อยู่ใน transaction แบบ InnoDB ดังนั้นเราใช้แบบนี้ (อยู่ใน transaction อยู่แล้ว)
    $stmt = $mysqli->prepare("SELECT id, name, price, stock FROM products WHERE id = ? FOR UPDATE");
    if (!$stmt) throw new Exception("Prepare products failed: " . $mysqli->error);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $prod_result = $stmt->get_result();
    $product = $prod_result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        throw new Exception("ไม่พบสินค้า");
    }

    // ตรวจ stock
    if ((int)$product['stock'] <= 0) {
        throw new Exception("สินค้าหมด");
    }

    // ตรวจสอบพ้อยในคอลัมน์ points (ใช้ integer)
    $user_points = (int)$user['points'];
    $price = (int)$product['price'];

    if ($user_points < $price) {
        throw new Exception("พ้อยของคุณไม่พอ!");
    }

    // หักพ้อย
    $new_points = $user_points - $price;
    $stmt = $mysqli->prepare("UPDATE users SET points = ? WHERE discord_id = ?");
    if (!$stmt) throw new Exception("Prepare update users failed: " . $mysqli->error);
    $stmt->bind_param("is", $new_points, $discord_id);
    if (!$stmt->execute()) {
        throw new Exception("ไม่สามารถอัปเดตพ้อย: " . $stmt->error);
    }
    $stmt->close();

    // ลด stock
    $stmt = $mysqli->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
    if (!$stmt) throw new Exception("Prepare update products failed: " . $mysqli->error);
    $stmt->bind_param("i", $product_id);
    if (!$stmt->execute()) {
        throw new Exception("ไม่สามารถอัปเดตสต็อก: " . $stmt->error);
    }
    $stmt->close();

    // บันทึกการซื้อ (เก็บ discord_id เพื่อเชื่อมกับ users)
    $stmt = $mysqli->prepare("INSERT INTO purchases (discord_id, product_id, price, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) throw new Exception("Prepare insert purchases failed: " . $mysqli->error);
    $stmt->bind_param("sii", $discord_id, $product_id, $price);
    if (!$stmt->execute()) {
        throw new Exception("ไม่สามารถบันทึกการซื้อ: " . $stmt->error);
    }
    $stmt->close();

    // commit
    $mysqli->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'ซื้อสินค้าสำเร็จ!',
        'points' => $new_points,
        'product' => $product['name']
    ]);
    exit;

} catch (Exception $e) {
    // rollback และส่งข้อความ error กลับไป (สำหรับ debug จะเห็นข้อความ error)
    $mysqli->rollback();
    $msg = $e->getMessage();
    // ถ้าต้องการซ่อนรายละเอียด error ใน production ให้เปลี่ยน message เป็นข้อความทั่วไป
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $msg
    ]);
    exit;
}
