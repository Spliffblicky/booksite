<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$street = $data["street"];
$city = $data["city"];
$state = $data["state"];
$zip = $data["zip"];
$user_id = $_GET["user_id"] ?? null;

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "No user ID"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO address (userid, street, city, state, zip) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $street, $city, $state, $zip);

echo $stmt->execute()
    ? json_encode(["status" => "success"])
    : json_encode(["status" => "error"]);
