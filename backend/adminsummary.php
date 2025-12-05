<?php
require "config.php";

$date = $_GET["date"];

$stmt = $conn->prepare("SELECT COUNT(*) AS orders, SUM(amount) AS revenue FROM orders WHERE DATE(order_date)=?");
$stmt->bind_param("s", $date);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "orders" => $res["orders"] ?? 0,
    "revenue" => $res["revenue"] ?? 0
]);
