<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data["order_id"];
$status = $data["status"];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

echo json_encode(["status" => $stmt->affected_rows > 0 ? "success" : "error"]);
