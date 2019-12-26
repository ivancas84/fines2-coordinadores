<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/array_unique_combine.php");
require_once("function/fecha_anios.php");
require_once("function/clasificaciones.php");
require_once("function/fecha_semestres.php");
require_once("function/dependencias.php");


$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if (isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Porcentaje Asignación";

$cursos = cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion);


$idCursos = array_unique_key($cursos, "id");
$asignaturas = array_unique_combine($cursos, "ch_asi_id", "ch_asi_nombre");

$render = new RenderAux();
$render->setAggregate(["_count"]);
$render->setGroup(["ch_asi_id"]);
$render->setCondition([ "id","=", $idCursos]);

$cursosCantidad = cursos_cantidad($idCursos);
$cursosAprobados = cursos_aprobados($idCursos);
$cursosFaltantes = cursos_faltantes($idCursos);

$total_aprobados = 0;
$total_faltantes = 0;
$total_asignaturas = 0;

$content = "porcentajeAsignacion/template.html";
require_once("index/menu.html");


function cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion){
  $filtros = [
    ["com_fecha_anio", "=", $fechaAnio],
    ["com_fecha_semestre", "=", $fechaSemestre],
    ["com_autorizada", "=", true],
    ["com_dvi_sed_dependencia", "=", $dependencia],
    ["com_dvi__clasificacion_nombre", "=", $clasificacion]
  ];
  
  $render = new Render();
  $render->setCondition($filtros);
  $render->setOrder(["ch_asi_nombre" => "ASC"]);
  
  $sql = EntitySqlo::getInstanceRequire("curso")->all($render);
  return Dba::fetchAll($sql);  
}

function cursos_cantidad($idCursos){
  $render = new RenderAux();
  $render->setAggregate(["_count"]);
  $render->setGroup(["ch_asi_id"]);
  $render->setCondition([ "id","=", $idCursos]);

  $sql = EntitySqlo::getInstanceRequire("curso")->advanced($render);
  return array_combine_key(Dba::fetchAll($sql), "ch_asi_id");
}

function cursos_aprobados($idCursos){
  $render = new RenderAux();
  $render->setAggregate(["_count"]);
  $render->setGroup(["ch_asi_id"]);
  $render->setCondition([
    [ "id","=", $idCursos],
    [ "toma_activa","=", true]
  ]);

  $sql = EntitySqlo::getInstanceRequire("curso")->advanced($render);
  $cursosAprobados = Dba::fetchAll($sql);
  if(empty($cursosAprobados)) return [];
  return array_combine_key($cursosAprobados, "ch_asi_id");
}

function cursos_faltantes($idCursos){
  $render = new RenderAux();
  $render->setAggregate(["_count"]);
  $render->setGroup(["ch_asi_id"]);
  $render->setCondition([
    [ "id","=", $idCursos],
    [ "toma_activa","=", false]
  ]);
  $sql = EntitySqlo::getInstanceRequire("curso")->advanced($render);
  $cursosFaltantes = Dba::fetchAll($sql);
  if(empty($cursosFaltantes)) return [];
  return array_combine_key($cursosFaltantes, "ch_asi_id");
}

function imprimir_aprobado($id){
  global $cursosAprobados;
  global $total_aprobados;

  if(!key_exists($id, $cursosAprobados)) return 0;
  $total_aprobados += intval($cursosAprobados[$id]["_count"]);
  return $cursosAprobados[$id]["_count"];
}

function imprimir_faltante($id){
  global $cursosFaltantes;
  global $total_faltantes;

  if(!key_exists($id, $cursosFaltantes)) return 0;
  $total_faltantes += intval($cursosFaltantes[$id]["_count"]);
  return $cursosFaltantes[$id]["_count"];
}

function imprimir_cantidad($id){
  global $cursosCantidad;
  global $total_asignaturas;

  $total_asignaturas += intval($cursosCantidad[$id]["_count"]);
  return $cursosCantidad[$id]["_count"];

}
