<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

class conexion
{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    public $conexion;

    public function __construct()
    {
        try {
            // Cargar variables de entorno desde la raíz del proyecto
            $dotenv = Dotenv::createImmutable(__DIR__ );
            $dotenv->load();

            // Asignar desde .env
            $this->server = $_ENV['DB_HOST'];
            $this->user = $_ENV['DB_USER'];
            $this->password = $_ENV['DB_PASSWORD'];
            $this->database = $_ENV['DB_NAME'];
            $this->port = $_ENV['DB_PORT'];

            $this->conexion = new mysqli(
                $this->server,
                $this->user,
                $this->password,
                $this->database,
                $this->port
            );

            $this->conexion->set_charset('utf8mb4');

            if ($this->conexion->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexion->connect_error);
            }
        } catch (Exception $e) {
            echo json_encode(['ERROR' => $e->getMessage()]);
            exit;
        }
    }

    public function test()
    {
        return $this->conexion;
    }

}
