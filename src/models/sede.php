<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";

class Sede extends BaseModelo
{

    public function listarSedes() {
        try {
            $result = $this->ejecutarSP("CALL sp_sede('listar', NULL, NULL)");
            $sedes = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            // Modificar el estado antes de enviarlo al frontend
            foreach ($sedes as &$sede) {
                $sede['estado'] = ($sede['estado'] == 0) ? 'Sede deshabilitada' : 'Activa';
            }

            $this->responderJson([
                'status' => 1,
                'message' => 'Sedes listadas correctamente',
                'data' => $sedes
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar sedes: ' . $e->getMessage()
            ]);
        }
    }

    public function consultarSede($id) {
        try {
            $result = $this->ejecutarSP("CALL sp_sede('listar', ?, NULL)",
            ["i", $id]);
            $sede = $result->fetch_assoc();
            $result->close();
            
            if ($sede) {
                $sede['estado'] = ($sede['estado'] == 0) ? 'Sede deshabilitada' : 'Activa';

                $this->responderJson([
                    'status' => 1,
                    'message' => 'Sede consultada correctamente',
                    'data' => $sede
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Sede no encontrada'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al consultar sede: ' . $e->getMessage()
            ]);
        }
    }

    public function listarSedesPorRectoria($idRectoria) {
        try {
            // Verificar que el parámetro $idRectoria no sea nulo
            if ($idRectoria === null) {
                throw new Exception("El ID de la rectoría es obligatorio.");
            }
    
            // Llamar al procedimiento almacenado con el parámetro $idRectoria
            $result = $this->ejecutarSP("CALL sp_sede('listar_por_rectoria', NULL, ?)",
            ["i", $idRectoria]);
            $sedes = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();
    
            // Responder con los datos obtenidos
            $this->responderJson([
                'status' => 1,
                'message' => 'Sedes listadas por rectoría correctamente',
                'data' => $sedes
            ]);
        } catch (Exception $e) {
            // Manejar errores y responder con un mensaje de error
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar sedes por rectoría: ' . $e->getMessage()
            ]);
        }
    }

    public function listarSedesPorUsuario() {
        try {
            $datos = $this->obtenerDatosDesdeToken();
            $permisos = $datos->permisos ?? [];
            $ids = array_unique(array_map(fn($p) => $p->id_sede, $permisos));
            if (empty($ids)) throw new Exception("Sin permisos");

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $tipos = str_repeat('i', count($ids));
            $sql = "SELECT id, nombre, id_rectoria FROM sede WHERE id IN ($placeholders)";
            $result = $this->ejecutarSP($sql, [$tipos, ...$ids]);
            $this->responderJson([
                'status' => 1,
                'message' => 'OK',
                'data' => $result->fetch_all(MYSQLI_ASSOC)
            ]);
        } catch (Exception $e) {
            $this->responderJson(['status' => 0, 'message' => $e->getMessage()]);
        }
    }
}

?>