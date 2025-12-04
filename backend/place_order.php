<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"];
$items = $data["items"];
$total = 0;
foreach ($items as $i) $total += $i["price"] * $i["quantity"];

$stmt = $conn->prepare("INSERT INTO orders (order_type, user_id, supplier_id, amount) VALUES ('user', ?, 0, ?)");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");

foreach ($items as $i) {
    $stmt_item->bind_param("iiid", $order_id, $i["book_id"], $i["quantity"], $i["price"]);
    $stmt_item->execute();
    $conn->query("UPDATE books SET quantity = quantity - {$i['quantity']} WHERE book_id={$i['book_id']}");
}

echo json_encode(["status" => "success", "order_id" => $order_id]);
