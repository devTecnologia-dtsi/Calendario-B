<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ . "/baseModelo.php";

class Logs extends BaseModelo
{
    public function listarLogs($limite, $offset) {
        $conexion = new conexion();
        $sql = $conexion->test()->prepare("CALL sp_logs('listar', NULL, ?, ?)");
        $sql->bind_param("ii", $limite, $offset);
        $sql->execute();
    
        // Obtener usuarios
        $result = $sql->get_result();
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        
        // Mover puntero al siguiente resultado (el total)
        $sql->next_result();
        $totalResult = $sql->get_result();
        $totalUsuarios = $totalResult->fetch_assoc()['total'];
    
        // Respuesta
        $this->responderJson([
            'status' => 1,
            'message' => 'Logs listados correctamente',
            'total' => $totalUsuarios,
            'data' => $usuarios
        ]);
    }
    

    public function buscarLogs($id)
    {
        try {
            $resultBuscarLogs = $this->ejecutarSp("CALL sp_logs('listar', ?, NULL, NULL)", ["i", $id]);
            $log = $resultBuscarLogs->fetch_assoc();
            $resultBuscarLogs->close();

            if ($log) {
                $this->responderJson([
                    'status' => 1,
                    'message' => 'Log encontrado correctamente',
                    'data' => $log
                ]);
            } else {
                $this->responderJson([
                    'status' => 0,
                    'message' => 'Log no encontrado'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al buscar log: ' . $e->getMessage()
            ]);
        }
    }
}
