<?php
require "config.php";

$result = $conn->query("SELECT * FROM books ORDER BY date_added DESC");

$books = [];
while ($row = $result->fetch_assoc()) $books[] = $row;

echo json_encode($books);
