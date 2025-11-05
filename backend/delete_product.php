<?php
require_once '../config.php';
$id = $_POST['id'];
$mysqli->query("DELETE FROM products WHERE id = $id");
header("Location: ../admin.php");
exit;
?>
