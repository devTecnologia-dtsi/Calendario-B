<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

// Cargar .env
$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();

// Retornar configuración
return [
    'jwt_secret' => $_ENV['JWT_SECRET_KEY']
];
