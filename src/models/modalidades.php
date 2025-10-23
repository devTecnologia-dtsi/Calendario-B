<?php 

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";

class Modalidades extends BaseModelo
{
    public function listarModalidades()
    {
        try {
            $result = $this->ejecutarSp("CALL sp_modalidades('listar', NULL, NULL, NULL, NULL)");
            $modalidades = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Modalidades listadas correctamente',
                'data' => $modalidades
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar modalidades: ' . $e->getMessage()
            ]);
        }
    }

    public function buscarModalidad($id)
    {
        try {
            $result = $this->ejecutarSp("CALL sp_modalidades('listar', ?, NULL, NULL, NULL)",
                ["i", $id]);
            $modalidad = $result->fetch_assoc();
            $result->close();

            if ($modalidad) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Modalidad encontrada',
                    'data' => $modalidad
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Modalidad no encontrada'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al buscar modalidad: ' . $e->getMessage()
            ]);
        }
    }

    public function crearModalidad($data)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultCrearModalidad = $this->ejecutarSp("CALL sp_modalidades('insertar', NULL, ?, ?, ?)",
                ["sis",
                $data['nombre'],
                $data['estado'],
                $usuarioAuth
            ]);
            
            // Capturar respuesta del SP
            $respuesta = $resultCrearModalidad->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al crear modalidad: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarModalidad($id, $data)
    {
        try {
            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultActualizarModalidad = $this->ejecutarSp("CALL sp_modalidades('actualizar', ?, ?, ?, ?)",
                ["isis",
                $id,
                $data['nombre'],
                $data['estado'],
                $usuarioAuth
            ]);

            // Capturar respuesta del SP
            $respuesta = $resultActualizarModalidad->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al actualizar modalidad: ' . $e->getMessage()
            ]);
        }
    }

    public function desactivarModalidad($id)
    {
        try {
            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_modalidades('deshabilitar', ?, NULL, NULL, ?)",
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
                'message' => 'Error al deshabilitar la modalidad: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarModalidad($id)
    {
        try {
            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $result = $this->ejecutarSp("CALL sp_modalidades('eliminar', ?, NULL, NULL, ?)",
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
                'message' => 'Error al eliminar la modalidad: ' . $e->getMessage()
            ]);
        }
    }
}
?>