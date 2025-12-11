<?php
require "config.php";
$data = json_decode(file_get_contents("php://input"), true);
$book_id = $data["book_id"];
$quantity = $data["quantity"];
$stmt = $conn->prepare("UPDATE books SET quantity = quantity + ? WHERE book_id = ?");
$stmt->bind_param("ii", $quantity, $book_id);
if ($stmt->execute()) echo json_encode(["status" => "success"]);
else echo json_encode(["status" => "error"]);
