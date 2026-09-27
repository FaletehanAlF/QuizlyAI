<?php

require_once "../../config/cors.php";

handle_cors();

require_once "../../config/database.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data["name"]) ||
    !isset($data["email"]) ||
    !isset($data["password"])
) {
    http_response_code(400);

    echo json_encode([
        "message" => "Name, email, and password are required"
    ]);

    exit;
}

if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid email format"
    ]);

    exit;
}

if (strlen($data["password"]) < 8) {
    http_response_code(400);

    echo json_encode([
        "message" => "Password must be at least 8 characters"
    ]);

    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data["email"]]);

if ($stmt->fetch()) {
    http_response_code(409);

    echo json_encode([
        "message" => "Email is already registered"
    ]);

    exit;
}

$passwordHash = password_hash(
    $data["password"],
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password)
    VALUES (?, ?, ?)
");

$stmt->execute([
    $data["name"],
    $data["email"],
    $passwordHash
]);