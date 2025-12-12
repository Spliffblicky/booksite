<?php
require "config.php";

header('Content-Type: application/json');
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/php_errors.log");

function generateOrderCode($conn)
{
    $q = $conn->query("SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1");

    if ($q && $q->num_rows > 0) {
        $last = intval($q->fetch_assoc()["order_id"]);
        $new = $last + 1;
    } else {
        $new = 1;
    }

    return str_pad($new, 4, "0", STR_PAD_LEFT);
}

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? null;
$items = $data["items"] ?? [];

if (!$user_id || empty($items)) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$total = 0;
foreach ($items as $i) {
    $total += $i["price"] * $i["quantity"];
}

$order_code = generateOrderCode($conn);

$stmt = $conn->prepare("INSERT INTO orders (order_id, order_type, userid, order_date, status, amount) VALUES (?, 'user', ?, NOW(), 'pending', ?)");
$stmt->bind_param("sid", $order_code, $user_id, $total);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to insert order"]);
    exit;
}

$order_id = $order_code;

$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, oi_quantity, price) VALUES (?, ?, ?, ?)");
foreach ($items as $i) {
    $stmt_item->bind_param("iiid", $order_id, $i["book_id"], $i["quantity"], $i["price"]);
    $stmt_item->execute();

    $conn->query("UPDATE books SET quantity = quantity - {$i['quantity']} WHERE book_id={$i['book_id']}");
}

echo json_encode(["status" => "success", "order_id" => $order_code]);
