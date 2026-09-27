<?php

// Helper verifikasi JWT untuk QuizlyAI.
// Dipakai endpoint yang butuh login: cukup panggil require_auth().

require_once __DIR__ . "/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Ambil Authorization header (kompatibel dengan PHP development server).
function get_authorization_header() {
    if (!empty($_SERVER["HTTP_AUTHORIZATION"])) {
        return $_SERVER["HTTP_AUTHORIZATION"];
    }

    // Fallback untuk server yang memindahkan header ke key lain.
    if (!empty($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])) {
        return $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
    }

    // Fallback terakhir via getallheaders() (case-insensitive).
    if (function_exists("getallheaders")) {
        foreach (getallheaders() as $name => $value) {
            if (strtolower($name) === "authorization") {
                return $value;
            }
        }
    }

    return null;
}

// Wajibkan request membawa Bearer token yang valid.
// Berhasil: return payload JWT (object: user_id, email, iat, exp).
// Gagal: kirim error JSON 401 lalu exit agar endpoint berhenti.
function require_auth() {
    header("Content-Type: application/json");

    $header = get_authorization_header();

    // 1. Header tidak ada sama sekali.
    if (!$header) {
        http_response_code(401);
        echo json_encode(["message" => "Authentication required"]);
        exit;
    }

    // 2. Format harus "Bearer <token>".
    $parts = explode(" ", $header, 2);
    if (count($parts) !== 2 || $parts[0] !== "Bearer" || empty(trim($parts[1]))) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid authorization header"]);
        exit;
    }

    $token = trim($parts[1]);

    // 3. Verifikasi tanda tangan + expired pakai secret/alg yang sama dengan jwt.php.
    try {
        return JWT::decode($token, new Key(JWT_SECRET, JWT_ALG));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid or expired token"]);
        exit;
    }
}
