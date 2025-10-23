<?php

use Firebase\JWT\JWT;

$config = require_once __DIR__ . "/../../config/config.php";
$secret = $config['jwt_secret'];

function generarJWT($datos): string {
    global $secret; // Acceder a la clave cargada arriba

    $payload = [
        "iss" => "http://localhost:82",
        "aud" => "http://localhost:82",
        "iat" => time(),
        "exp" => time() + (20 * 60), // 20 minutos
        "data" => $datos
    ];

    return JWT::encode($payload, $secret, 'HS256');
}


