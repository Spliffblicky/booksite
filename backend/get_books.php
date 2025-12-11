<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");
require "config.php";

$result = $conn->query("SELECT * FROM books ORDER BY date_added DESC");

$books = [];
$baseURL = "http://localhost:8080/site/";

while ($row = $result->fetch_assoc()) {
    $imagePath = $row['image_path'] ? $baseURL . $row['image_path'] : $baseURL . "images/default.jpg";

    $books[] = [
        "book_id" => $row['book_id'],
        "bookname" => $row['bookname'],
        "author" => $row['author'],
        "description" => $row['description'],
        "price" => $row['price'],
        "quantity" => $row['quantity'],
        "date_added" => $row['date_added'],
        "image_path" => $imagePath,
        "supplier_id" => $row['supplier_id']
    ];
}

echo json_encode($books);
$conn->close();
