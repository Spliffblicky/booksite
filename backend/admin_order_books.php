<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$book_id = $data["book_id"];
$quantity = $data["quantity"];
$supplier_id = $data["supplier_id"];
$amount = $data["amount"];

$order_code = generateOrderCode($conn);

$stmt = $conn->prepare("INSERT INTO orders (order_id, order_type, supplier_id, order_date, status, amount) VALUES (?, 'admin', ?, NOW(), 'pending', ?)");
$stmt->bind_param("sid", $order_code, $supplier_id, $amount);
$stmt->execute();
$order_id = $stmt->insert_id;

$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, oi_quantity, price) VALUES (?, ?, ?, ?)");
$stmt_item->bind_param("iiid", $order_id, $book_id, $quantity, $amount);
$stmt_item->execute();

$conn->query("UPDATE books SET quantity = quantity + $quantity WHERE book_id=$book_id");

echo json_encode(["status" => "success", "order_id" => $order_code]);
