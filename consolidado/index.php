<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");

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
  $render = new Render();
  $render->setGeneralCondition([ //filtros para cursos
    ["com_dvi__clasificacion_nombre", "=", $clasificacion]
  ]);
  $render->setCondition([
    ["com_fecha_anio", "=", $fechaAnio],
    ["com_fecha_semestre", "=", $fechaSemestre],
    ["com_autorizada", "=", true],
    ["com_dvi_sed_dependencia", "=", $dependencia],
  ]);
 
  $render->setOrder(["com_dvi_sed_numero" => "ASC", "com_anio" => "ASC", "com_semestre" => "ASC"]);

  $sql = EntitySqlo::getInstanceRequire("curso")->all($render);
  return Dba::fetchAll($sql);
}

function tomas($idCursos){
  $filtros = [ //filtros para tomas
    ["cur_id", "=", $idCursos],
    ["estado_contralor", "!=", "Modificar"],
  ];
  
  $render = new Render();
  $render->setCondition($filtros);
  $render->setOrder(["cur_com_dvi_sed_numero" => "ASC", "cur_com_anio" => "ASC", "cur_com_semestre" => "ASC"]);
  
  $sql = EntitySqlo::getInstanceRequire("toma")->all($render);
  return Dba::fetchAll($sql);
}

function cantidad_alumnos($idComisiones){
  $render = new RenderAux();
  $render->setAggregate(["_cantidad"]);
  $render->setGroup(["comision"]);
  $render->setCondition([
    ["com_id","=",$idComisiones],
    ["activo","=",true]
  ]);

  $sql = EntitySqlo::getInstanceRequire("nomina2")->advanced($render);

  $cantidadAlumnos_ = Dba::fetchAll($sql);
  return array_combine_key($cantidadAlumnos_, "comision");
}

function comision_values($comision) {
  global $cantidadAlumnos;

  $curso_ = reset($comision);
  $idComision = $curso_["comision"];
  $ret = EntitySqlo::getInstanceRequire("curso")->values($curso_);
  $ret["alumnos"] = key_exists($idComision, $cantidadAlumnos) ? $cantidadAlumnos[$idComision]["_cantidad"] : 0; 
  return $ret;
}



