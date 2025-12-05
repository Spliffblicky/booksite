<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["username"]) || !isset($data["id"]) || !isset($data["password"])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$username = $data["username"];
$id = $data["id"];
$password = $data["password"];

$stmt = $conn->prepare("SELECT userid, role, password FROM users WHERE username=? AND userid=?");
$stmt->bind_param("si", $username, $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status" => "error"]);
    exit;
}

$user = $res->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    echo json_encode(["status" => "error"]);
    exit;
}

echo json_encode([
    "status" => "success",
    "user_id" => $user["userid"],
    "role" => $user["role"]
]);
