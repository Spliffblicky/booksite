<?php
require "config.php";


$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["username"]) || !isset($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Username and password required"]);
    exit;
}

$username = $data["username"];
$password = $data["password"];

$stmt = $conn->prepare("SELECT userid, role, password FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

$user = $res->fetch_assoc();


if (!password_verify($password, $user["password"])) {
    echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    exit;
}


echo json_encode([
    "status" => "success",
    "user_id" => $user["userid"],
    "username" => $username,
    "role" => $user["role"]
]);
