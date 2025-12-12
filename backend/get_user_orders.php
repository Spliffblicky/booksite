<?php
require 'config.php';


$query = "
SELECT o.order_id, o.user_id, u.username, o.status, o.total_amount,
       oi.book_id, b.bookname, oi.quantity, oi.price
FROM orders o
JOIN users u ON o.user_id = u.userid
JOIN order_items oi ON oi.order_id = o.order_id
JOIN books b ON oi.book_id = b.book_id
WHERE u.role = 'user'
ORDER BY o.order_id DESC
";

$res = $conn->query($query);

$orders = [];
while ($row = $res->fetch_assoc()) {
    $id = $row['order_id'];

    if (!isset($orders[$id])) {
        $orders[$id] = [
            "order_id" => $row["order_id"],
            "userid" => $row["userid"],
            "username" => $row["username"],
            "status" => $row["status"],
            "total" => $row["amount"],
            "items" => []
        ];
    }

    $orders[$id]["items"][] = [
        "book_id" => $row["book_id"],
        "bookname" => $row["bookname"],
        "quantity" => $row["quantity"],
        "price" => $row["price"]
    ];
}

echo json_encode(array_values($orders));
