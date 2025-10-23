<?php 

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ ."/baseModelo.php";

class PeriodoAcademico extends BaseModelo
{
    public function listarPeriodosAcademicos()
    {
        try {
            $result = $this->ejecutarSp("CALL sp_periodo_academico('listar', NULL, NULL, NULL, NULL, NULL)");
            $periodos = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Períodos académicos listados correctamente',
                'data' => $periodos
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar períodos: ' . $e->getMessage()
            ]);
        }
    }

    public function buscarPeriodoAcademico($id)
    {
        try {
            $result = $this->ejecutarSp("CALL sp_periodo_academico('listar', ?, NULL, NULL, NULL, NULL)",
            ["i",
            $id
        ]);
            $periodo = $result->fetch_assoc();
            $result->close();

            if ($periodo) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Período encontrado',
                    'data' => $periodo
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Período no encontrado'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al buscar período: ' . $e->getMessage()
            ]);
        }
    }

    public function crearPeriodoAcademico($data)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_periodo('insertar', NULL, ?, ?, ?, ?)",
            ["iiis",
                $data['anio'],
                $data['periodo'], 
                $data['estado'],
                $usuarioAuth
            ]);

            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al crear período: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarPeriodoAcademico($id, $data)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_periodo('actualizar', ?, ?, ?, ?, ?)",
            ["iiiis", 
                $id, 
                $data['anio'], 
                $data['periodo'], 
                $data['estado'],
                $usuarioAuth
            ]);

            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al actualizar período: ' . $e->getMessage()
            ]);
        }
    }

    public function deshabilitarPeriodoAcademico($id)
    {
        try {
            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_periodo('deshabilitar', ?, NULL, NULL, NULL, ?)",
            ["is", 
                $id,
                $usuarioAuth
                
            ]);

            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al deshabilitar período: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarPeriodoAcademico($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_periodo('eliminar', ?, NULL, NULL, NULL, ?)",
                ["is",
                $id,
                $usuarioAuth
            ]);

            $respuesta = $result->fetch_assoc();
            $this->responderJson($respuesta);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al eliminar período: ' . $e->getMessage()
            ]);
        }
    }
}
