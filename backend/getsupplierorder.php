<?php
header('Content-Type: application/json');
require "config.php";

if (!isset($_GET["userid"]) || !is_numeric($_GET["userid"])) {
    echo json_encode([]);
    exit;
}

$supplier_id = (int)$_GET["userid"];

$stmt = $conn->prepare("SELECT * FROM orders WHERE supplier_id=? ORDER BY order_date DESC");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$res = $stmt->get_result();

$orders = [];
while ($row = $res->fetch_assoc()) $orders[] = $row;

echo json_encode($orders);

$stmt->close();
$conn->close();
