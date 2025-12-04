<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data["order_id"];
$status = $data["status"];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
$stmt->bind_param("si", $status, $order_id);

echo $stmt->execute()
    ? json_encode(["status" => "success"])
    : json_encode(["status" => "error"]);
