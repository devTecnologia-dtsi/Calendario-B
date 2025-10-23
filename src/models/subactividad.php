<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";

class Subactividad extends BaseModelo
{
    public function listarSubactividad()
    {
        try {
            $resultListarSubactividad = $this->ejecutarSp("CALL sp_subactividad('listar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $subactividad = $resultListarSubactividad->fetch_all(MYSQLI_ASSOC);
            $resultListarSubactividad->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Subactividades listadas correctamente.',
                'data' => $subactividad
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar las subactividades. ' . $e->getMessage()
            ]);        
        }
    }

    public function buscarSubactividadPorActividad($id_actividad)
    {
        try {
            $resultBuscarSubactividad = $this->ejecutarSp("CALL sp_subactividad('listar_por_actividad', NULL, ?, NULL, NULL, NULL, NULL, NULL, NULL)", 
            ["i", $id_actividad]);
            $subactividad = $resultBuscarSubactividad->fetch_all(MYSQLI_ASSOC);
            $resultBuscarSubactividad->close();

            if ($subactividad) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Subactividades encontradas.',
                    'data' => $subactividad
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'No se encontraron subactividades.'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al buscar las subactividades. ' . $e->getMessage()
            ]);
        }
    }

    public function crearSubactividad($dato)
    {
        if (!isset($dato['id_actividad'], $dato['nombre'], $dato['descripcion'], $dato['estado'], $dato['fecha_inicio'], $dato['fecha_fin'])) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Faltan datos requeridos para insertar subactividad.'
            ]);
        }

        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultado = $this->ejecutarSp("CALL sp_subactividad('insertar', NULL, ?, ?, ?, ?, ?, ?, ?)",
                ["ississs",
                    $dato['id_actividad'],
                    $dato['nombre'],
                    $dato['descripcion'],
                    $dato['estado'],
                    $dato['fecha_inicio'],
                    $dato['fecha_fin'],
                    $usuarioAuth
                ]);
            $respuesta = $resultado->fetch_assoc();
            $resultado->close();

            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al crear la subactividad. ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarSubactividad($id, $dato)
    {
        if (!isset($dato['id_actividad'], $dato['nombre'], $dato['descripcion'], $dato['estado'], $dato['fecha_inicio'], $dato['fecha_fin'])) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Faltan datos requeridos para actualizar subactividad.'
            ]);
        }

        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultado = $this->ejecutarSp("CALL sp_subactividad('actualizar', ?, ?, ?, ?, ?, ?, ?, ?)",
                ["iississs",
                    $id,
                    $dato['id_actividad'],
                    $dato['nombre'],
                    $dato['descripcion'],
                    $dato['estado'],
                    $dato['fecha_inicio'],
                    $dato['fecha_fin'],
                    $usuarioAuth
                ]);
            $respuesta = $resultado->fetch_assoc();
            $resultado->close();

            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al actualizar la subactividad. ' . $e->getMessage()
            ]);
        }
    }

    public function deshabilitarSubactividad($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultado = $this->ejecutarSp("CALL sp_subactividad('deshabilitar', ?, NULL, NULL, NULL, NULL, NULL, NULL, ?)",
            ["is", 
                $id,
                $usuarioAuth
            ]);
            $respuesta = $resultado->fetch_assoc();
            $resultado->close();

            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al deshabilitar la subactividad. ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarSubactividad($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultado = $this->ejecutarSp("CALL sp_subactividad('eliminar', ?, NULL, NULL, NULL, NULL, NULL, NULL, ?)",
            ["is", 
                $id,
                $usuarioAuth

            ]);
            $respuesta = $resultado->fetch_assoc();
            $resultado->close();

            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al eliminar la subactividad. ' . $e->getMessage()
            ]);
        }
    }
}
