<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"];
$items = $data["items"];

$total = 0;
foreach ($items as $i) $total += $i["price"] * $i["quantity"];

$first = $items[0]["book_id"];
$q = $conn->prepare("SELECT supplier_id FROM books WHERE book_id=?");
$q->bind_param("i", $first);
$q->execute();
$supplier = $q->get_result()->fetch_assoc();
$supplier_id = $supplier["supplier_id"];

$stmt = $conn->prepare("INSERT INTO orders (order_type, user_id, supplier_id, order_date, status, amount) VALUES ('user', ?, ?, NOW(), 'pending', ?)");
$stmt->bind_param("iid", $user_id, $supplier_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, oi_quantity, price) VALUES (?, ?, ?, ?)");

foreach ($items as $i) {
    $stmt_item->bind_param("iiid", $order_id, $i["book_id"], $i["quantity"], $i["price"]);
    $stmt_item->execute();
    $conn->query("UPDATE books SET quantity = quantity - {$i['quantity']} WHERE book_id={$i['book_id']}");
}

echo json_encode(["status" => "success", "order_id" => $order_id]);
