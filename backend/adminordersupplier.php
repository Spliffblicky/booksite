<?php
require "config.php";

$result = $conn->query("SELECT * FROM orders WHERE order_type='supplier'");
$orders = [];
while ($row = $result->fetch_assoc()) $orders[] = $row;

echo json_encode($orders);
