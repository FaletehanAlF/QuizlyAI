<?php

// Konfigurasi JWT terpusat untuk QuizlyAI.
// Dipakai oleh login.php untuk membuat token setelah email/password valid.

require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;

// Secret key untuk tanda tangan token (HS256).
// Tahap development: cukup simpan di sini.
// Production nanti: pindahkan ke environment variable.
define("JWT_SECRET", "bad012df7efe047f1f045c136ebe9f67a3a72aa68ab50ad5e5de5f6d517ea19f");

// Algoritma yang dipakai. HS256 = HMAC + SHA-256, sederhana dan cukup untuk belajar.
define("JWT_ALG", "HS256");

// Masa berlaku token: 24 jam (86400 detik).
// Alasan: cukup lama agar nyaman saat development,
// tapi tetap expired agar token tidak berlaku selamanya.
define("JWT_EXPIRE", 86400);

// Membuat JWT berisi user_id, email, iat, dan exp.
// Tidak pernah menyimpan password / password hash di dalam token.
function create_jwt($user_id, $email) {
    $now = time();

    $payload = [
        "user_id" => $user_id,
        "email" => $email,
        "iat" => $now,
        "exp" => $now + JWT_EXPIRE
    ];

    return JWT::encode($payload, JWT_SECRET, JWT_ALG);
}
