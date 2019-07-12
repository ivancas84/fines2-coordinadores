<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/clasificaciones.php");
require_once("function/dependencias.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;



$title = "Consolidado";

$cursos = cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion);

$comisiones = [];
if (!empty($cursos)){
  $idCursos = array_unique_key($cursos, "id");
  $idComisiones = array_unique_key($cursos, "comision");
  $tomas = tomas($idCursos);
  $cantidadAlumnos = cantidad_alumnos($idComisiones);
  $comisiones = array_group_value($cursos, "comision");
  $tomas = array_group_value($tomas, "curso");
}

$content = "consolidado/template.html";
require_once("index/menu.html");



function cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion){
  $filtros = [ //filtros para cursos
    ["com_fecha_anio", "=", $fechaAnio],
    ["com_fecha_semestre", "=", $fechaSemestre],
    ["com_autorizada", "=", true],
    ["com_dvi_sed_dependencia", "=", $dependencia],
    ["com_dvi__clasificacion_nombre", "=", $clasificacion]
  ];

  $render = new Render();
  $render->setAdvanced($filtros);
  $render->setOrder(["com_dvi_sed_numero" => "ASC", "com_anio" => "ASC", "com_semestre" => "ASC"]);

  $sql = CursoSqlo::getInstance()->all($render);
   return Dba::fetchAll($sql);
}

function tomas($idCursos){
  $filtros = [ //filtros para tomas
    ["cur_id", "=", $idCursos],
    ["estado_contralor", "!=", "Modificar"],
  ];
  
  $render = new Render();
  $render->setAdvanced($filtros);
  $render->setOrder(["cur_com_dvi_sed_numero" => "ASC", "cur_com_anio" => "ASC", "cur_com_semestre" => "ASC"]);
  
  $sql = TomaSqlo::getInstance()->all($render);
  return Dba::fetchAll($sql);
}

function cantidad_alumnos($idComisiones){
  $render = new RenderAux();
  $render->setAggregate(["_cantidad"]);
  $render->setGroup(["comision"]);
  $render->setAdvanced([
    ["com_id","=",$idComisiones],
    ["activo","=",true]
  ]);

  $sql = Nomina2Sqlo::getInstance()->advanced($render);

  $cantidadAlumnos_ = Dba::fetchAll($sql);
  return array_combine_key($cantidadAlumnos_, "comision");
}


function get_comision($comision) {
  global $cantidadAlumnos;

  $curso_ = reset($comision);
  $idComision = $curso_["comision"];
  $curso = CursoSqlo::getInstance()->json($curso_);
  $ret = [
    "sede" => new SedeValues($curso["comision_"]["division_"]["sede_"]),
    "domicilio" => new DomicilioValues($curso["comision_"]["division_"]["sede_"]["domicilio_"]),
    "division" => new DivisionValues($curso["comision_"]["division_"]),
    "comision" => new ComisionValues($curso["comision_"]),
    "coordinador" => (!empty($toma["profesor_"])) ? new IdPersonaValues($curso["comision_"]["division_"]["sede_"]["coordinador_"]) : null,
    "plan" => new PlanValues($curso["comision_"]["division_"]["plan_"]),
    "alumnos" => key_exists($idComision, $cantidadAlumnos) ? $cantidadAlumnos[$idComision]["_cantidad"] : 0, 
  ];

  return $ret;
}

function get_curso($curso) {
  $c = CursoSqlo::getInstance()->json($curso);

  $ret = [
    "curso" => new CursoValues($c),
    "asignatura" => new AsignaturaValues($c["carga_horaria_"]["asignatura_"]),
  ];

  return $ret;
}

function get_toma($toma) {
  $toma = TomaSqlo::getInstance()->json($toma);
  $ret = [
    "toma" => new TomaValues($toma),
    "profesor" => (!empty($toma["profesor_"])) ? new IdPersonaValues($toma["profesor_"]) : "Toma Sin Docente",
  ];
  return $ret;
}