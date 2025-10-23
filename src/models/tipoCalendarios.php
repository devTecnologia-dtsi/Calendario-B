<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";

class TipoCalendarios extends BaseModelo
{
    public function listarTipoCalendario() {
        try {
            $result = $this->ejecutarSP("CALL sp_tipoCalendario('listar', NULL)");
            $rectorias = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Tipos de calendarios listados correctamente',
                'data' => $rectorias
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar tipos de calendarios: ' . $e->getMessage()
            ]);
        }
    }
    
    public function consultarTipoCalendario($id) {
        try {
            $result = $this->ejecutarSP("CALL sp_tipoCalendario('listar', ?)", 
                ["i", $id]);
            $rectoria = $result->fetch_assoc();
            $result->close();

            if ($rectoria) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Tipo de calendario consultado correctamente',
                    'data' => $rectoria
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Tipo de calendario no encontrado'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al consultar tipo de calendarios: ' . $e->getMessage()
            ]);
        }
    }
}


?>