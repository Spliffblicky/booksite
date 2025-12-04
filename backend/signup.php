<?php
require "config.php";

$role = $_POST['role'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (role, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $role, $username, $email, $phone, $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "user_id" => $stmt->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Signup failed"]);
}
