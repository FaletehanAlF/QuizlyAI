<?php

// Konfigurasi CORS terpusat untuk QuizlyAI (tahap development).
// Dipakai semua endpoint API agar bisa diakses dari frontend Next.js.

// Origin frontend yang diizinkan.
// Tidak memakai "*" karena API memakai JWT via header Authorization.
define("CORS_ALLOWED_ORIGINS", [
    "http://localhost:3000"
]);

define("CORS_ALLOWED_METHODS", "GET, POST, PUT, DELETE, OPTIONS");
define("CORS_ALLOWED_HEADERS", "Content-Type, Authorization");

// Pasang header CORS dan tangani preflight OPTIONS.
// Preflight: balas 204 lalu exit. Request lain: lanjut ke logic endpoint.
function handle_cors() {
    $origin = $_SERVER["HTTP_ORIGIN"] ?? "";

    // Hanya origin yang terdaftar yang mendapat header Allow-Origin.
    if (in_array($origin, CORS_ALLOWED_ORIGINS, true)) {
        header("Access-Control-Allow-Origin: $origin");
    }

    header("Access-Control-Allow-Methods: " . CORS_ALLOWED_METHODS);
    header("Access-Control-Allow-Headers: " . CORS_ALLOWED_HEADERS);

    if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "OPTIONS") {
        http_response_code(204);
        exit;
    }
}
