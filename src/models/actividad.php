<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ ."/baseModelo.php";


class Actividad extends BaseModelo
{

    public function listarActividad ()
    {
        try {
            $result = $this->ejecutarSp("CALL sp_actividad('listar', NULL, NULL, NULL, NULL, NULL)");
            $actividades = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Actividades listadas correctamente',
                'data' => $actividades
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar actividades: ' . $e->getMessage()
            ]);
        }
    }

    public function buscarActividadPorCalendario($id_calendario)
    {
        try {
            $result = $this->ejecutarSp("CALL sp_actividad('listar_por_calendario', NULL, ?, NULL, NULL, NULL)",
            ["i", $id_calendario]);
            $actividad = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            if ($actividad) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Actividades encontradas',
                    'data' => $actividad
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'No se encontraron actividades para el calendario especificado'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al buscar actividades: ' . $e->getMessage()
            ]);
        }
    }

    public function insertarActividad($dato)
    {
        if (!isset($dato['id_calendario'], $dato['titulo'], $dato['estado'])) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Faltan datos requeridos para insertar actividad'
            ]);
        }
        try {
            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp(
                "CALL sp_actividad('insertar', NULL, ?, ?, ?, ?)",
                ["isss", 
                    $dato['id_calendario'], 
                    $dato['titulo'], 
                    $dato['estado'],
                    $usuarioAuth
                ]
            );

            // Capturar respuesta SP
            $respuesta = $result->fetch_assoc();
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Actividad insertada correctamente',
                'data' => $respuesta
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al insertar actividad: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarActividad($id, $dato)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            // Validar datos requeridos
            $result = $this->ejecutarSp("CALL sp_actividad('actualizar', ?, ?, ?, ?, ?)", 
            ["iisis", 
                $id, 
                $dato['id_calendario'], 
                $dato['titulo'], 
                $dato['estado'], 
                $usuarioAuth
            ]);
        
            // Capturar respuesta del SP
            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al actualizar actividad: ' . $e->getMessage()
            ]);
        }
    }

    public function desactivarActividad($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_actividad('deshabilitar', ?, NULL, NULL, NULL, ?)",
                ["is", 
                $id,
                $usuarioAuth
            ]);

            // Capturar respuesta del SP
            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al deshabilitar actividad: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarActividad($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_actividad('eliminar', ?, NULL, NULL, NULL, ?)",
                ["is", 
                $id,
                $usuarioAuth
            ]);

            // Capturar respuesta del SP
            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al eliminar actividad: ' . $e->getMessage()
            ]);
        }
    }
}