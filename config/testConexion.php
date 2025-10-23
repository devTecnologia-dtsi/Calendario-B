<?php
require_once __DIR__ . '/conexion.php';

$conexion = new conexion();

if ($conexion->test()) {
    echo "Conexión exitosa";
} else {
    echo "Error en la conexión";
}