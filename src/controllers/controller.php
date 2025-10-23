<?php
include_once __DIR__ . "/../models/actividad.php";
include_once __DIR__ . "/../models/subactividad.php";
include_once __DIR__ . "/../models/periodoAcademico.php";
include_once __DIR__ . "/../models/calendario.php";
include_once __DIR__ . "/../models/usuario.php";
include_once __DIR__ . "/../models/rectoria.php";
include_once __DIR__ . "/../models/sede.php";
include_once __DIR__ . "/../models/rol.php";
include_once __DIR__ . "/../models/tipoCalendarios.php";
include_once __DIR__ . "/../models/logs.php";
include_once __DIR__ . "/../models/modalidades.php";
include_once __DIR__ . "/../models/tiposPeriodo.php";

// Consultar
function consultar($id, $tabla, $limite = 5, $offset = 0) {
    $modelo = null;

    switch ($tabla) {
        case 'actividad':
            $modelo = new Actividad();
            break;
        case 'subactividad':
            $modelo = new Subactividad();
            break;
        case 'periodo':
            $modelo = new PeriodoAcademico();
            break;
        case 'calendario':
            $modelo = new Calendario();
            break;
        case 'usuario':
            $modelo = new CrudUsuario();
            break;
        case 'rectoria':
            $modelo = new Rectoria();
            break;
        case 'rectoriasPorUsuario':
            $modelo = new Rectoria();
            break;
        case 'sede':
            $modelo = new Sede();
            break;
        case 'sedesPorRectoria':
            $modelo = new Sede();
            break;
        case 'sedesPorUsuario':
            $modelo = new Sede();
            break;
        case 'rol':
            $modelo = new Rol();
            break;
        case 'tipoCalendarios':
            $modelo = new TipoCalendarios();
            break;
        case 'modalidad':
            $modelo = new Modalidades();
            break;
        case 'periodoAcademico':
            $modelo = new PeriodoAcademico();
            break;
        case 'logs':
            $modelo = new Logs();
            break;
        default:
            echo json_encode(array('ERROR' => 'No se ha encontrado la tabla'));
            return;

        if ($modelo) {
            $id ? $modelo->buscar($id) : $modelo->listar($limite, $offset);
        }
    }
}


// INSERTAR
function insertar($dato, $tabla) {
    switch ($tabla) {
        case 'actividad':
            $actividad = new Actividad();
            $actividad->insertarActividad($dato);
            break;
        case 'subactividad':
            $subactividad = new Subactividad();
            $subactividad->crearSubactividad($dato);
        case 'periodo':
            $periodo = new PeriodoAcademico();
            $periodo->crearPeriodoAcademico($dato);
            break;
        case 'calendario':
            $calendario = new Calendario();
            $calendario->insertarCalendario($dato);
            break;
        case 'usuario':
            $usuario = new CrudUsuario();
            $usuario->insertarUsuario($dato);
            break;
        default:
            echo json_encode(array('ERROR' => 'No se ha encontrado la tabla para insertar'));
            break;
    }
}

// ACTUALIZAR
function actualizar($id, $dato, $tabla) {
    switch ($tabla) {
        case 'actividad':
            $actividad = new Actividad();
            $actividad->actualizarActividad($id, $dato);
            break;
        case 'periodoAcademico':
            $periodo = new PeriodoAcademico();
            $periodo->actualizarPeriodoAcademico($$id, $dato);
            break;
        case 'calendario':
            $calendario = new Calendario();
            $calendario->actualizarCalendarioCompleto($$id, $dato);
            break;
        case 'usuario':
            $usuario = new CrudUsuario();
            $usuario->actualizarUsuario($id, $dato);
            break;
        case 'rol':
            $rol = new Rol();
            $rol->actualizarRol($id, $dato);
            break;
        case 'modalidad':
            $modalidad = new Modalidades();
            $modalidad->actualizarModalidad($id, $dato);
            break;
        default:
            echo json_encode(array('ERROR' => 'No se ha encontrado la tabla para actualizar'));
            break;
    }
}

// DESHABILITAR
function desactivar($id, $tabla) {
    switch ($tabla) {
        case 'usuario':
            $usuario = new CrudUsuario();
            $usuario->desactivarUsuario($id);
            break;
        case 'actividad':
            $actividad = new Actividad();
            $actividad->desactivarActividad($id);
            break;
        case 'calendario':
            $calendario = new Calendario();
            $calendario->deshabilitarCalendario($id);
            break;
        case 'modalidad':
            $modalidad = new Modalidades();
            $modalidad->desactivarModalidad($id);
            break;
        case 'periodoAcademico':
            $periodo = new PeriodoAcademico();
            $periodo->deshabilitarPeriodoAcademico($id);
            break;

        default:
            echo json_encode(array('ERROR' => 'No se puede deshabilitar en la tabla ' . $tabla));
            break;
    }
}

// ELIMINAR
function eliminar($id, $tabla) {
    switch ($tabla) {
        case 'actividad':
            $actividad = new Actividad();
            $actividad->eliminarActividad($id);
            break;
        case 'periodoAcademico':
            $periodo = new PeriodoAcademico();
            $periodo->eliminarPeriodoAcademico($id);
            break;
        case 'calendario':
            $calendario = new Calendario();
            $calendario->eliminarCalendario($id);
            break;
        case 'usuario':
            $usuario = new CrudUsuario();
            $usuario->eliminarUsuario($id);
            break;
        case 'modalidad':
            $modalidad = new Modalidades();
            $modalidad->eliminarModalidad($id);
            break;
        default:
            echo json_encode(array('ERROR' => 'No se ha encontrado la tabla para eliminar'));
            break;
    }
}

?>