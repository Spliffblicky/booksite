<?php
require "config.php";
$res = $conn->query("SELECT o.order_id, o.user_id, o.total, o.date, u.username FROM orders o JOIN users u ON o.user_id=u.userid ORDER BY o.date DESC");
$orders = [];
while ($row = $res->fetch_assoc()) {
    $items_res = $conn->query("SELECT b.bookname, oi.quantity FROM order_items oi JOIN books b ON oi.book_id=b.book_id WHERE oi.order_id=" . $row['order_id']);
    $items = [];
    while ($i = $items_res->fetch_assoc()) $items[] = $i;
    $row['items'] = $items;
    $orders[] = $row;
}
echo json_encode($orders);
$conn->close();
