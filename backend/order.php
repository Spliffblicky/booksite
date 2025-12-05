<?php
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$order_type = $data['order_type'] ?? null;
$items = $data['items'] ?? [];
$supplier_id = $data['supplier_id'] ?? null;

if (!$user_id || !$order_type || empty($items)) {
    echo json_encode(["status" => "error"]);
    exit;
}

$total = 0;
foreach ($items as $item) $total += $item['price'] * $item['oi_quantity'];

if ($order_type === 'user') {
    $first = $items[0]['book_id'];
    $q = $conn->prepare("SELECT supplier_id FROM books WHERE book_id=?");
    $q->bind_param("i", $first);
    $q->execute();
    $res = $q->get_result()->fetch_assoc();
    $supplier_id = $res['supplier_id'] ?? null;
}

if (!$supplier_id) {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO orders (order_type, user_id, supplier_id, order_date, status, amount) VALUES (?, ?, ?, NOW(), 'pending', ?)");
$stmt->bind_param("siid", $order_type, $user_id, $supplier_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, oi_quantity, price) VALUES (?, ?, ?, ?)");

foreach ($items as $item) {
    $stmt_item->bind_param("iiid", $order_id, $item['book_id'], $item['oi_quantity'], $item['price']);
    $stmt_item->execute();
}

echo json_encode([
    "status" => "success",
    "order_type" => $order_type,
    "order_id" => $order_id
]);
