<?php
require "config.php";

$supplier_id = $_GET["supplier_id"];

$stmt = $conn->prepare("SELECT * FROM orders WHERE supplier_id=? ORDER BY order_date DESC");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$res = $stmt->get_result();

$orders = [];
while ($row = $res->fetch_assoc()) $orders[] = $row;

echo json_encode($orders);
