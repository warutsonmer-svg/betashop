<?php
require_once '../config.php';
$id = $_POST['id'];

// ✅ อัปเดตสถานะ
$mysqli->query("UPDATE topups SET status='approved' WHERE id='$id'");

// ✅ เพิ่มยอดเงินให้ user
$topup = $mysqli->query("SELECT user_id, amount FROM topups WHERE id='$id'")->fetch_assoc();
$uid = $topup['user_id'];
$amount = $topup['amount'];
$mysqli->query("UPDATE users SET balance = balance + $amount WHERE id='$uid'");

header("Location: ../admin.php");
exit;
?>
