<?php
require "config.php";
$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data["order_id"];
$stmt = $conn->prepare("UPDATE orders SET status='complete' WHERE order_id=?");
$stmt->bind_param("i", $order_id);
if ($stmt->execute()) echo json_encode(["status" => "success"]);
else echo json_encode(["status" => "error"]);
