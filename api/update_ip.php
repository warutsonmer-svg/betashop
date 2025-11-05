<?php
error_reporting(0);
ini_set('display_errors', 0);

require '../config.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception('Unauthorized');
    }

    $discord_id = $mysqli->real_escape_string($_SESSION['user']['id']);
    $product_id = (int)($_POST['product_id'] ?? 0);
    $new_ip = trim($_POST['new_ip'] ?? '');

    if (!$product_id || !$new_ip) {
        throw new Exception('ข้อมูลไม่ครบ');
    }

    $sql = "SELECT ip_address, last_ip_change 
            FROM purchases 
            WHERE discord_id='$discord_id' AND product_id=$product_id
            LIMIT 1";
    $res = $mysqli->query($sql);
    if (!$res || $res->num_rows === 0) {
        throw new Exception('ไม่พบสินค้านี้ในบัญชีของคุณ');
    }

    $row = $res->fetch_assoc();
    $last_change = $row['last_ip_change'] ? strtotime($row['last_ip_change']) : 0;
    $now = time();

    if ($last_change && ($now - $last_change) < 5 * 3600) {
        $remain = 5 * 3600 - ($now - $last_change);
        $h = floor($remain / 3600);
        $m = floor(($remain % 3600) / 60);
        throw new Exception("กรุณารออีก {$h} ชั่วโมง {$m} นาที เพื่อเปลี่ยน IP ได้อีกครั้ง");
    }

    $mysqli->query("UPDATE purchases 
                    SET ip_address='$new_ip', last_ip_change=NOW() 
                    WHERE discord_id='$discord_id' AND product_id=$product_id");

    echo json_encode(['status' => 'success', 'message' => 'อัปเดต IP สำเร็จ!', 'ip' => $new_ip]);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
