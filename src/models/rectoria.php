<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";


class Rectoria extends BaseModelo
{
    public function listarRectorias() {
        try {
            $result = $this->ejecutarSP("CALL sp_rectoria('listar', NULL)");
            $rectorias = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Rectorías listadas correctamente',
                'data' => $rectorias
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar rectorías: ' . $e->getMessage()
            ]);
        }
    }

    public function listarRectoriasPorUsuario() {
        try {
            $datos = $this->obtenerDatosDesdeToken();
            $permisos = $datos->permisos ?? [];
            $ids = array_unique(array_map(fn($p) => $p->id_rectoria, $permisos));
            if (empty($ids)) throw new Exception("Sin permisos");

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $tipos = str_repeat('i', count($ids));
            $sql = "SELECT id, nombre FROM rectoria WHERE id IN ($placeholders)";
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


    
    public function consultarRectoria($id) {
        try {
            $result = $this->ejecutarSP("CALL sp_rectoria('listar', ?)", 
                ["i", $id]);
            $rectoria = $result->fetch_assoc();
            $result->close();

            if ($rectoria) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Rectoría consultada correctamente',
                    'data' => $rectoria
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Rectoría no encontrada'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al consultar rectoría: ' . $e->getMessage()
            ]);
        }
    }
}

?>