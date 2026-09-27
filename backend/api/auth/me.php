<?php

require_once "../../config/cors.php";

handle_cors();

require_once "../../config/database.php";
require_once "../../config/auth.php";

header("Content-Type: application/json");

// Tolak request tanpa Bearer token yang valid (401 ditangani di dalam).
$auth = require_auth();

// Ambil user berdasarkan user_id dari payload JWT.
$stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$auth->user_id]);

$user = $stmt->fetch();

// Token valid tapi user sudah tidak ada di database.
if (!$user) {
    http_response_code(404);

    echo json_encode([
        "message" => "User not found"
    ]);

    exit;
}

http_response_code(200);

echo json_encode([
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "created_at" => $user["created_at"]
    ]
]);
