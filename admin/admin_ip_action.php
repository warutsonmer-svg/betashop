<?php
require '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['id'], $admin_ids)) {
  die(json_encode(['status'=>'error','msg'=>'ไม่มีสิทธิ์']));
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$id = (int)($data['id'] ?? 0);
$new_ip = $data['new_ip'] ?? '';

if (!$id) die(json_encode(['status'=>'error','msg'=>'ข้อมูลไม่ถูกต้อง']));

switch ($action) {
  case 'reset':
    $mysqli->query("UPDATE purchases SET ip_address=NULL, last_ip_change=NOW() WHERE id=$id");
    echo json_encode(['status'=>'success','msg'=>'รีเซ็ต IP เรียบร้อย']);
    break;

  case 'add':
    if (!filter_var($new_ip, FILTER_VALIDATE_IP)) {
      echo json_encode(['status'=>'error','msg'=>'รูปแบบ IP ไม่ถูกต้อง']);
      exit;
    }
    $mysqli->query("UPDATE purchases SET ip_address='$new_ip', last_ip_change=NOW() WHERE id=$id");
    echo json_encode(['status'=>'success','msg'=>'เพิ่ม IP สำเร็จ']);
    break;

  case 'delete':
    $mysqli->query("UPDATE purchases SET ip_address=NULL WHERE id=$id");
    echo json_encode(['status'=>'success','msg'=>'ลบ IP สำเร็จ']);
    break;

  case 'ban':
    $mysqli->query("UPDATE purchases SET ip_address='BANNED', last_ip_change=NOW() WHERE id=$id");
    echo json_encode(['status'=>'success','msg'=>'แบน IP สำเร็จ']);
    break;

  default:
    echo json_encode(['status'=>'error','msg'=>'คำสั่งไม่ถูกต้อง']);
}
?>
