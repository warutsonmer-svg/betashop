<?php
require_once '../config.php';

$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

$stmt = $mysqli->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $name, $price, $description);
$stmt->execute();

header("Location: ../admin.php");
exit;
?>
