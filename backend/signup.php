<?php
header('Content-Type: application/json');
require "config.php";

$role = $_POST['role'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : '';

if (!$role || !$username || !$email || !$phone || !$password) {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("SELECT userid FROM users WHERE username=? OR email=?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error"]);
    exit;
}
$stmt->close();

if ($role === 'user') {
    $prefix = '10';
    $stmt = $conn->prepare("SELECT userid FROM users WHERE role='user' ORDER BY userid DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($lastId);
    $stmt->fetch();
    $stmt->close();
    $num = $lastId ? (int)substr($lastId, strlen($prefix)) + 1 : 1;
    $userid = $prefix . $num;
} elseif ($role === 'supplier') {
    $prefix = '110';
    $stmt = $conn->prepare("SELECT userid FROM users WHERE role='supplier' ORDER BY userid DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($lastId);
    $stmt->fetch();
    $stmt->close();
    $num = $lastId ? (int)substr($lastId, strlen($prefix)) + 1 : 1;
    $userid = $prefix . $num;
} else {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (userid, role, username, email, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $userid, $role, $username, $email, $phone, $password);

echo json_encode([
    "status" => $stmt->execute() ? "success" : "error",
    "user_id" => $userid
]);

$stmt->close();
$conn->close();
