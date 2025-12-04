<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$order_type = $data['order_type'] ?? 'user';
$items = $data['items'] ?? [];

if (!$user_id || empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['oi_quantity'];
}


$stmt = $conn->prepare("INSERT INTO orders (order_type, o.user_id, order_date, status, amount) VALUES (?, ?, NOW(), 'pending', ?)");
$stmt->bind_param("iid", $order_type, $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;


$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, oi_quantity, price) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $stmt_item->bind_param("iiid", $order_id, $item['book_id'], $item['oi_quantity'], $item['price']);
    $stmt_item->execute();
}

echo json_encode(['status' => 'success', 'order_id' => $order_id]);
