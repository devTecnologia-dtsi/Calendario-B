<?php

require_once __DIR__ . "/../../vendor/autoload.php";
include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

abstract class BaseModelo
{
    public function __construct()
    {
        // Cargar variables de entorno si no están cargadas aún
        if (!isset($_ENV['JWT_SECRET'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../config/');
            $dotenv->load();
        }
    }

    protected function ejecutarSp($query, $params = [])
    {
        $conexion = new conexion();
        $sql = $conexion->test()->prepare($query);

        if (!empty($params)) {
            $tipos = array_shift($params); // Extrae tipos como "s", "is", etc.
            $sql->bind_param($tipos, ...$params);
        }

        $sql->execute();
        $result = $sql->get_result();
        $sql->close();

        return $result;
    }

    protected function responderJson($respuesta)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta);
        exit;
    }

    protected function obtenerCorreoDesdeToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            $config = require __DIR__ . '/../../config/config.php';
            $secret = $config['jwt_secret'] ?? null;

            if (!$secret) {
                http_response_code(401);
                echo json_encode(["error" => "Token inválido: JWT_SECRET no definido"]);
                exit;
            }

            try {
                $decoded = JWT::decode($token, new Key($secret, 'HS256'));
                return $decoded->data->correo ?? null;
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(["error" => "Token inválido"]);
                exit;
            }
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Token no proporcionado"]);
            exit;
        }
    }

    protected function obtenerDatosDesdeToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];

        try {
            $config = require __DIR__ . '/../../config/config.php';

            $secret = $config['jwt_secret'];
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return $decoded->data ?? null;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Token inválido: " . $e->getMessage()]);
            exit;
        }
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Token no proporcionado"]);
        exit;
    }
}

}

?>