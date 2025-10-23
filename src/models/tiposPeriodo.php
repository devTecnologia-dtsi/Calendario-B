<?php 

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ ."/baseModelo.php";

class TiposPeriodo extends BaseModelo
{
    public function listarTipoPeriodo()
    {
        try {
            $resultListarTipoPeriodos = $this->ejecutarSp("CALL sp_tipos_periodo('listar', NULL, NULL, NULL)");
            $tiposPeriodo = $resultListarTipoPeriodos->fetch_all(MYSQLI_ASSOC);
            $resultListarTipoPeriodos->close();

            $this->responderJson([
                'status' => 1,
                'message' => 'Periodos listados correctamente.',
                'data' => $tiposPeriodo
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar tipos de periodo: ' . $e->getMessage()
            ]);
        }
    }

    public function buscarTipoPeriodo($id)
    {
        try {
            $resultBuscarTipoPeriodo = $this->ejecutarSp("CALL sp_tipos_periodo('listar', ?, NULL, NULL)",
                ["i", $id]);
            $tipoPeriodo = $resultBuscarTipoPeriodo->fetch_assoc();
            $resultBuscarTipoPeriodo->close();

        if($tipoPeriodo) {
            $this->responderJSON([
                'status' => 1,
                'message' => 'Tipo de periodo encontrado.',
                'data' => $tipoPeriodo
            ]);
        } else {
            $this->responderJSON([
                'status' => 0,
                'message' => 'Tipo de periodo no encontrado.',
            ]);        }

        } catch (Exception $e) {
            $this->responderJSON([
                'status' => 0,
                'message' => 'Error al buscar Tipo de periodo' . $e->getMessage()
            ]);
        }
    }
}

?>