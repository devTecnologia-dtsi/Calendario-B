<?php

include_once __DIR__ . "/../controllers/controller.php";
include_once __DIR__ . "/../../config/cors.php";

// Detectar método HTTP y path
$metodo = $_SERVER['REQUEST_METHOD'];
// $path = $_SERVER['PATH_INFO'] ?? '/';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
// var_dump($_SERVER['PATH_INFO']);

// Extraer partes del path
$partesPath = explode('/', trim($path, '/'));
$tabla = $partesPath[0] ?? null;
$accion = $partesPath[1] ?? null;
$id = isset($partesPath[1]) && is_numeric($partesPath[1]) ? intval($partesPath[1]) : null;

// Captura de parámetros limite y offset desde la URL (paginación)
$limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Leer el cuerpo de la petición si es necesario
$dato = json_decode(file_get_contents('php://input'), true);

// Validar si se pasó una tabla válida
if (!$tabla) {
    http_response_code(400);
    echo json_encode(["error" => "Tabla no especificada"]);
    exit;
}

// Manejo de métodos
switch ($metodo) {
    case 'GET':

        if ($tabla === 'rectoria') {
            //Consultar rectorías
            $rectoria = new Rectoria();
            if ($id) {
                $rectoria->consultarRectoria($id);
            } else {
                $rectoria->listarRectorias();
            }
        }

        elseif ($tabla === 'rectoriasPorUsuario') {
            $rectoria = new Rectoria();
            $rectoria->listarRectoriasPorUsuario();
        }

        elseif ($tabla === 'sede') {
            //Consultar sedes
            $sede = new Sede();
            if ($id) {
                $sede->consultarSede($id);
            } else {
                $sede->listarSedes();
            }
        } 
        
        elseif ($tabla === 'sedesPorRectoria') {
            // Listar sedes por rectoría
            $sede = new Sede();
            if ($id) {
                $sede->listarSedesPorRectoria($id);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID de rectoría no especificado"]);
                exit;
            }
        } 
        
        elseif ($tabla === 'sedesPorUsuario') {
            // Listar sedes por usuario
            $sede = new Sede();
            $sede->listarSedesPorUsuario();
        }

        elseif ($tabla === 'rol') {
            // Consultar rol específico o todos
            $rol = new Rol();
            if ($id) {
                $rol->consultarRol($id);
            } else {
                $rol->listarRol();
            }
        } 
        
        elseif ($tabla === 'usuario') {
            // Consultar usuario específico o todos
            $usuario = new CrudUsuario();
            if ($id) {
                $usuario->consultarUsuario($id);
            } else {
                $usuario->listarUsuarios($limite, $offset);
            }
        }

        elseif ($tabla === 'actividad') {
            // Ver una actividad específica o todas
            $actividad = new Actividad();
            if ($id) {
                $actividad->buscarActividadPorCalendario($id);
            } else {
                $actividad->listarActividad();
            }
        }

        elseif ($tabla === 'subactividad') {
            // Ver una subactividad específica o todas
            $subactividad = new SubActividad();
            if ($id) {
                $subactividad->buscarSubactividadPorActividad($id);
            } else {
                $subactividad->listarSubActividad();
            }
        }
        
        elseif ($tabla === 'tipoCalendarios') {
            // Ver un tipo de calendario especifico o todos
            $tipoCalendario = new TipoCalendarios();
            if ($id) {
                // Consultar tipo de calendario por ID
                $tipoCalendario->consultarTipoCalendario($id);
            } else {
                // Listar todos los tipos de calendario
                $tipoCalendario->listarTipoCalendario();
            }
        } 

        elseif ($tabla === 'tiposPeriodo') {
            $tiposPeriodo = new TiposPeriodo();
            if($id){
                $tiposPeriodo->buscarTipoPeriodo($id);
            } else {
                $tiposPeriodo->listarTipoPeriodo();
            }
        }
        
        elseif ($tabla === 'calendario') {
            // Ver un calendario especifico o todos
            $calendario = new Calendario();
            if ($id) {
                // Consultar calendario por ID
                $calendario->consultarCalendarioParaEdicion($id);
            } else {
                // Listar todos los calendarios
                $calendario->listarCalendario();
            }
        } 
        
        elseif ($tabla === 'periodoAcademico') {
            // Ver un periodo especifico o todos
            $periodo = new PeriodoAcademico();
            if ($id) {
                // Consultar periodo por ID
                $periodo->buscarPeriodoAcademico($id);
            } else {
                // Listar todos los periodos
                $periodo->listarPeriodosAcademicos();
            }
        }

        elseif ($tabla === 'modalidad') {
            // Ver una modalidad especifica o todas
            $modalidad = new Modalidades();
            if ($id) {
                // Consultar modalidad por ID
                $modalidad->buscarModalidad($id);
            } else {
                // Listar todas las modalidades
                $modalidad->listarModalidades();
            }
        }

        elseif ($tabla === 'logs') {
            // Ver un log especifico o todos
            $logs = new Logs();
            if ($id) {
                // Consultar log por ID
                $logs->buscarLogs($id);
            } else {
                // Listar todos los logs
                $logs->listarLogs($limite, $offset);
            }
        }
        
        else {
            consultar($id, $tabla, $limite, $offset);
        }
        break;

    case 'POST':

        if ($tabla === 'usuario'){
            //insertar usuario
            $usuario = new CrudUsuario();
            $usuario->insertarUsuario($dato);
        }

        elseif ($tabla === 'actividad') {
            // Insertar actividad
            $actividad = new Actividad();
            $actividad->insertarActividad($dato);
        }

        elseif ($tabla === 'subactividad') {
            // Insertar subactividad
            $subactividad = new SubActividad();
            $subactividad->crearSubactividad($dato);
        }

        elseif ($tabla === 'periodo') {
            // Insertar periodo
            $periodo = new PeriodoAcademico();
            $periodo->crearPeriodoAcademico($dato);
        }

        elseif ($tabla === 'calendario') {
            // Insertar calendario
            $calendario = new Calendario();
            $calendario->insertarCalendario($dato);
        }

        elseif ($tabla === 'modalidad') {
            // Insertar modalidad
            $modalidad = new Modalidades();
            $modalidad->crearModalidad($dato);
        }

        elseif ($tabla === 'usuarioPorCorreo') {
            if (!isset($dato['correo'])) {
                http_response_code(400);
                echo json_encode(["error" => "Correo no especificado"]);
                exit;
            }
        
            $usuario = new CrudUsuario();
            $usuario->obtenerUsuarioPorCorreo($dato['correo']);
        }
        
        
        else {
            insertar($dato, $tabla);
        }

        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(["error" => "ID no especificado para actualización"]);
            exit;
        }

        if ($tabla === 'usuario') {
            // Actualizar usuario
            $usuario = new CrudUsuario();
            $usuario->actualizarUsuario($id, $dato);
        }

        elseif ($tabla === 'rol'){
            // Actualizar rol
            $rol = new Rol();
            $rol->actualizarRol($id, $dato);
        }

        elseif ($tabla === 'actividad') {
            // Actualizar actividad
            $actividad = new Actividad();
            // var_dump($dato); exit;
            $actividad->actualizarActividad($id, $dato);
        }

        elseif ($tabla === 'subactividad') {
            // Actualizar subactividad
            $subactividad = new SubActividad();
            $subactividad->actualizarSubactividad($id, $dato);
        }

        else if ($tabla === 'periodoAcademico') {
            // Actualizar periodo
            $periodo = new PeriodoAcademico();
            $periodo->actualizarPeriodoAcademico($id, $dato);
        }

        elseif ($tabla === 'calendario') {
            // Actualizar calendario
            $calendario = new Calendario();
            $calendario->actualizarCalendarioCompleto($id, $dato);
        }

        elseif ($tabla === 'modalidad') {
            // Actualizar modalidad
            $modalidad = new Modalidades();
            $modalidad->actualizarModalidad($id, $dato);
        }
        
        else {
            actualizar($id, $dato, $tabla);
        }
        break;

    case 'PATCH':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(["error" => "ID no especificado para deshabilitación"]);
            exit;
        }

        if ($tabla === 'usuario') {
            // Deshabilitar usuario
            $usuario = new CrudUsuario();
            $usuario->desactivarUsuario($id);
        }

        elseif ($tabla === 'actividad') {
            // Deshabilitar actividad
            $actividad = new Actividad();
            $actividad->desactivarActividad($id);
        }

        elseif ($tabla === 'subactividad') {
            // Deshabilitar subactividad
            $subactividad = new SubActividad();
            $subactividad->deshabilitarSubactividad($id);
        }
        
        elseif ($tabla === 'calendario') {
            // Deshabilitar calendario
            $calendario = new Calendario();
            $calendario->deshabilitarCalendario($id);
        }

        elseif ($tabla === 'modalidad') {
            // Deshabilitar modalidad
            $modalidad = new Modalidades();
            $modalidad->desactivarModalidad($id);
        }

        elseif ($tabla === 'periodoAcademico') {
            // Deshabilitar periodo academico
            $periodo = new PeriodoAcademico();
            $periodo->deshabilitarPeriodoAcademico($id);
        }

        else {
            desactivar($id, $tabla);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(["error" => "ID no especificado para eliminación"]);
            exit;
        }
        
        if( $tabla === 'usuario') {
            // Eliminar usuario
            $usuario = new CrudUsuario();
            $usuario->eliminarUsuario($id);
        }

        elseif ($tabla === 'actividad') {
            // Eliminar actividad
            $actividad = new Actividad();
            $actividad->eliminarActividad($id);
        }

        elseif ($tabla === 'subactividad') {
            // Eliminar subactividad
            $subactividad = new SubActividad();
            $subactividad->eliminarSubactividad($id);
        }
        
        elseif ($tabla === 'periodoAcademico') {
            // Eliminar periodo
            $periodo = new PeriodoAcademico();
            $periodo->eliminarPeriodoAcademico($id);
        }

        elseif ($tabla === 'calendario') {
            // Eliminar calendario
            $calendario = new Calendario();
            $calendario->eliminarCalendario($id);
        }

        elseif ($tabla === 'modalidad') {
            // Eliminar modalidad
            $modalidad = new Modalidades();
            $modalidad->eliminarModalidad($id);
        }

        else {
            eliminar($id, $tabla);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([["error" => "Método no permitido"]]);
        break;
}
