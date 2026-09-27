<?php

require_once "../../config/database.php";
require_once "../../config/jwt.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data["email"]) ||
    !isset($data["password"]) ||
    empty(trim($data["email"])) ||
    empty($data["password"])
) {
    http_response_code(400);

    echo json_encode([
        "message" => "Email and password are required"
    ]);

    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->execute([trim($data["email"])]);

$user = $stmt->fetch();

if (!$user || !password_verify($data["password"], $user["password"])) {
    http_response_code(401);

    echo json_encode([
        "message" => "Invalid email or password"
    ]);

    exit;
}

try {
    $token = create_jwt($user["id"], $user["email"]);
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        "message" => "Failed to create token"
    ]);

    exit;
}

http_response_code(200);

echo json_encode([
    "message" => "Login successful",
    "token" => $token,
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"]
    ]
]);
