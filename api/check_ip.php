<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

// 🔒 ปิดการแสดง error บนหน้าเว็บ (ป้องกันข้อมูลหลุด)
error_reporting(0);
ini_set('display_errors', 0);

// 🔒 กันการเรียกข้ามหน้า (CORS / Unauthorized Request Protection)
$allowed_domains = [
    'http://localhost',             // ใช้ตอนทดสอบในเครื่อง
    'http://127.0.0.1',
    '',                             // ✅ อนุญาตให้ Python หรือ Script เรียกตรงได้ (ไม่มี Referer)
    'https://yourdomain.com',       // 👉 เปลี่ยนตอนเอาขึ้นโฮสต์จริง
    'https://www.yourdomain.com'
];

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$origin  = $_SERVER['HTTP_ORIGIN'] ?? '';

$valid_request = false;
foreach ($allowed_domains as $domain) {
    // ✅ ถ้าอนุญาตให้เรียกตรง (ไม่มี Referer/Origin)
    if ($domain === '' && !$referer && !$origin) {
        $valid_request = true;
        break;
    }

    // ✅ ตรวจว่ามี Referer หรือ Origin ตรงกับโดเมนที่อนุญาต
    if (str_starts_with($referer, $domain) || str_starts_with($origin, $domain)) {
        $valid_request = true;
        break;
    }
}

// ❌ ถ้าไม่ผ่าน ให้หยุดเลย
if (!$valid_request) {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized request source']);
    exit;
}

// ✅ รับพารามิเตอร์
$product = $_GET['product'] ?? '';
$ip = $_GET['ip'] ?? '';

if (!$product || !$ip) {
    echo json_encode(['status' => 'error', 'msg' => 'Missing parameters']);
    exit;
}

// ✅ ป้องกัน SQL Injection
$product = $mysqli->real_escape_string($product);
$ip = $mysqli->real_escape_string($ip);

// ✅ ฟังก์ชันตรวจสอบ IP ว่ามีสิทธิ์หรือไม่
function checkIP($mysqli, $product, $ip) {
    $sql = "SELECT ph.id
            FROM purchases AS ph
            JOIN products AS p ON p.id = ph.product_id
            WHERE p.name = '$product' AND ph.ip_address = '$ip'
            LIMIT 1";
    $res = $mysqli->query($sql);
    return ($res && $res->num_rows > 0);
}

// ✅ ตรวจสอบซ้ำ 3 รอบ (กัน lag / network delay)
$max_attempts = 3;
$success = false;

for ($i = 1; $i <= $max_attempts; $i++) {
    if (checkIP($mysqli, $product, $ip)) {
        $success = true;
        break;
    }
    usleep(500000); // รอ 0.5 วินาทีต่อรอบ
}

// ✅ ตอบกลับ JSON
if ($success) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'denied']);
}
?>
