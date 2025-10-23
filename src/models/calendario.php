<?php

include_once __DIR__ . "/../../config/conexion.php";
include_once __DIR__ . "/../../config/cors.php";
include_once __DIR__ ."/baseModelo.php";


class Calendario extends BaseModelo
{
    public function listarCalendario()
    {
        try {
            $resulListarCalendario = $this->ejecutarSp("CALL sp_calendario('listar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $calendarios = $resulListarCalendario->fetch_all(MYSQLI_ASSOC);
            $resulListarCalendario->close();

            $this->responderJson([
                'sattus' => 1,
                'message' => 'Calendarios listados correctamente',
                'data' => $calendarios
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al listar calendarios: ' . $e->getMessage()
            ]);
        }
    }

    public function consultarCalendarioParaEdicion($id)
    {
        try {
            // Consultar el calendario base
            $result = $this->ejecutarSp("CALL sp_calendario('listar', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)",
            ["i",
            $id
        ]);
            $calendario = $result->fetch_assoc();
            $result->close();
    
            if (!$calendario) {
                return $this->responderJson([
                    'status' => 0,
                    'message' => 'Calendario no encontrado'
                ]);
            }
    
            // Consultar actividades
            $actividades = $this->ejecutarSp("CALL sp_actividad('listar_por_calendario', NULL, ?, NULL, NULL, NULL)",
            ["i", $id]);
            $actividadesArray = [];
    
            while ($actividad = $actividades->fetch_assoc()) {
                // Consultar subactividades por cada actividad
                $subactividades = $this->ejecutarSp("CALL sp_subactividad('listar_por_actividad', NULL, ?, NULL, NULL, NULL, NULL, NULL, NULL)",
                ["i", $actividad['id']]);
                $subactividadesArray = [];
    
                while ($sub = $subactividades->fetch_assoc()) {
                    $subactividadesArray[] = $sub;
                }
    
                $actividad['subactividades'] = $subactividadesArray;
                $actividadesArray[] = $actividad;
            }
    
            $calendario['actividades'] = $actividadesArray;
    
            return $this->responderJson([
                'status' => 1,
                'message' => 'Calendario a editar obtenido',
                'data' => $calendario
            ]);
        } catch (Exception $e) {
            return $this->responderJson([
                'status' => 0,
                'message' => 'Error al obtener el calendario: ' . $e->getMessage()
            ]);
        }
    }

    public function insertarCalendario($data)
    {
        if (

            empty($data['id_usuario']) || 
            empty($data['id_rectoria']) || 
            empty($data['id_sede']) || 
            empty($data['id_tipo_calendario']) || 
            empty($data['id_modalidad']) || 
            empty($data['id_periodo_academico']) || 
            empty($data['id_tipo_periodo']) || 
            !isset($data['estado'])
        ) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Faltan datos requeridos para crear el calendario'
            ]);
            return;
        }
    
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            // Crear calendario base
            $resultado = $this->ejecutarSp(
                "CALL sp_calendario('insertar', NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    'iiiiiiiiis',
                    $data['id_usuario'],
                    $data['id_rectoria'],
                    $data['id_sede'],
                    $data['id_tipo_calendario'],
                    $data['id_modalidad'],
                    $data['id_periodo_academico'],
                    $data['id_tipo_periodo'],
                    $data['estado'],
                    $data['en_sede'],
                    $usuarioAuth
                ]
            );
            $res = $resultado->fetch_assoc();
            $idCalendario = $res['id_calendario'];
            $resultado->close();
    
            // Crear actividades
            if (isset($data['actividades']) && is_array($data['actividades'])) {
                foreach ($data['actividades'] as $actividad) {
                    $resActividad = $this->ejecutarSp(
                        "CALL sp_actividad('insertar', NULL, ?, ?, ?, ?)",
                        [
                            'isss',
                            $idCalendario,
                            $actividad['titulo'],
                            $actividad['estado'],
                            $usuarioAuth
                        ]
                    );
                    $actividadCreada = $resActividad->fetch_assoc();
                    $idActividad = $actividadCreada['id_actividad'];
                    $resActividad->close();
    
                    // Crear subactividades
                    if (isset($actividad['subactividades']) && is_array($actividad['subactividades'])) {
                        foreach ($actividad['subactividades'] as $subactividad) {
                            $this->ejecutarSp(
                                "CALL sp_subactividad('insertar', NULL, ?, ?, ?, ?, ?, ?, ?)",
                                [
                                    'ississs',
                                    $idActividad,
                                    $subactividad['nombre'],
                                    $subactividad['descripcion'],
                                    $subactividad['estado'],
                                    $subactividad['fecha_inicio'],
                                    $subactividad['fecha_fin'],
                                    $usuarioAuth
                                ]
                            )->close();
                        }
                    }
                }
            }
    
            $this->responderJson([
                'status' => 1,
                'message' => 'Calendario creado exitosamente',
                'id_calendario' => $idCalendario
            ]);
    
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al crear calendario completo: ' . $e->getMessage()
            ]);
        }
    }
 
    public function actualizarCalendarioCompleto($id, $data)
    {
        if (
            empty($data['id_usuario']) || 
            empty($data['id_rectoria']) || 
            empty($data['id_sede']) || 
            empty($data['id_tipo_calendario']) || 
            empty($data['id_modalidad']) || 
            empty($data['id_periodo_academico']) || 
            !isset($data['estado'])
        ) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Faltan datos requeridos para actualizar el calendario'
            ]);
            return;
        }
    
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            // 1. Actualizar calendario
            $this->ejecutarSp(
                "CALL sp_calendario('actualizar', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    'iiiiiiiiiis',
                    $id,
                    $data['id_usuario'],
                    $data['id_rectoria'],
                    $data['id_sede'],
                    $data['id_tipo_calendario'],
                    $data['id_modalidad'],
                    $data['id_periodo_academico'],
                    $data['id_tipo_periodo'],
                    $data['estado'],
                    $data['en_sede'],
                    $usuarioAuth
                ]
            )->close();
    
            // 2. Procesar actividades
            if (isset($data['actividades']) && is_array($data['actividades'])) {
                foreach ($data['actividades'] as $actividad) {
    
                    if (isset($actividad['id']) && $actividad['id'] > 0) {
                        // Actualizar actividad
                        $this->ejecutarSp(
                            "CALL sp_actividad('actualizar', ?, ?, ?, ?, ?)",
                            [
                                'iisis',
                                $actividad['id'],
                                $id,
                                $actividad['titulo'],
                                $actividad['estado'],
                                $usuarioAuth
                            ]
                        )->close();
                        $idActividad = $actividad['id'];
                    } else {
                        // Insertar nueva actividad
                        $resAct = $this->ejecutarSp(
                            "CALL sp_actividad('insertar', NULL, ?, ?, ?, ?)",
                            [
                                'isss',
                                $id,
                                $actividad['titulo'],
                                $actividad['estado'],
                                $usuarioAuth
                            ]
                        );
                        $actividadInsertada = $resAct->fetch_assoc();
                        $idActividad = $actividadInsertada['id_actividad'];
                        $resAct->close();
                    }
    
                    // 3. Procesar subactividades
                    if (isset($actividad['subactividades']) && is_array($actividad['subactividades'])) {
                        foreach ($actividad['subactividades'] as $subactividad) {
    
                            if (isset($subactividad['id']) && $subactividad['id'] > 0) {
                                // Actualizar subactividad
                                $this->ejecutarSp(
                                    "CALL sp_subactividad('actualizar', ?, ?, ?, ?, ?, ?, ?, ?)",
                                    [
                                        'iississs',
                                        $subactividad['id'],
                                        $idActividad,
                                        $subactividad['nombre'],
                                        $subactividad['descripcion'],
                                        $subactividad['estado'],
                                        $subactividad['fecha_inicio'],
                                        $subactividad['fecha_fin'],
                                        $usuarioAuth
                                    ]
                                )->close();
                            } else {
                                // Insertar nueva subactividad
                                $this->ejecutarSp(
                                    "CALL sp_subactividad('insertar', NULL, ?, ?, ?, ?, ?, ?, ?)",
                                    [
                                        'ississs',
                                        $idActividad,
                                        $subactividad['nombre'],
                                        $subactividad['descripcion'],
                                        $subactividad['estado'],
                                        $subactividad['fecha_inicio'],
                                        $subactividad['fecha_fin'],
                                        $usuarioAuth
                                    ]
                                )->close();
                            }
                        }
                    }
                }
            }
    
            $this->responderJson([
                'status' => 1,
                'message' => 'Calendario actualizado exitosamente.'
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al actualizar calendario completo: ' . $e->getMessage()
            ]);
        }
    }
    
    public function deshabilitarCalendario($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $resultDesactivarCalendario = $this->ejecutarSp("CALL sp_calendario('deshabilitar', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ?)",
                ["is", 
                $id,
                $usuarioAuth
            ]);
            
            $respuesta = $resultDesactivarCalendario->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al deshabilitar calendario: ' . $e->getMessage()
            ]);
        }
    }

    // No se usa, pero se deja funcional en caso de implementación
    public function eliminarCalendario($id)
    {
        try {

            // Obtener correo desde el token
            $usuarioAuth = $this->obtenerCorreoDesdeToken();

            $respuestaEliminarCalendario = $this->ejecutarSp("CALL sp_calendario('eliminar', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ?)",
                ["is", 
                $id,
                $usuarioAuth
            ]);

                // Capturar respuesta del SP
            $respuesta = $respuestaEliminarCalendario->fetch_assoc();
            $this->responderJson($respuesta);

        } catch (Exception $e) {
            $this->responderJson([
                'status' => 0,
                'message' => 'Error al eliminar calendario: ' . $e->getMessage()
            ]);
        }
    }
}
