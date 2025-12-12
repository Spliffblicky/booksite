<?php
require "config.php";
header("Content-Type: application/json");
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/php_errors.log");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error"]);
    exit;
}

$street = $_POST["street"] ?? null;
$city = $_POST["city"] ?? null;
$state = $_POST["state"] ?? null;
$zip = $_POST["zip"] ?? null;
$user_id = $_POST["user_id"] ?? null;

if (!$street || !$city || !$state || !$zip || !$user_id) {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO addresses (userid, street, city, state, zip) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $street, $city, $state, $zip);

echo $stmt->execute()
    ? json_encode(["status" => "success"])
    : json_encode(["status" => "error"]);
$stmt->close();
$conn->close();
