<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");




$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : null;
require_once("_periodoClasificacion/options.php");

$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

if(!$fechaAnio || !$fechaSemestre || !$clasificacion) { 
  $content = "_periodoClasificacion/template.html";
  require_once("index/menu.html");
  return;
}

$title = "Porcentaje Asignación";

$filtros = [ //filtros para cursos
  ["com_fecha_anio", "=", $fechaAnio],
  ["com_fecha_semestre", "=", $fechaSemestre],
  ["com_autorizada", "=", true],
  ["com_dvi_sed_dependencia", "=", $dependencia],
  ["com_dvi__clasificacion_nombre", "=", $clasificacion]
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["com_dvi_sed_numero" => "ASC", "com_anio" => "ASC", "com_semestre" => "ASC"]);

$sql = CursoSqlo::getInstance()->all($render);
$cursos = Dba::fetchAll($sql);
$idCursos = array_unique_key($cursos, "id");


$render = new RenderAux();
$render->setAggregate(["_cantidad"]);
$render->setGroup(["ch_asi_id"]);
$render->setCondition([ "id","=", $idCursos]);

$sql = CursoSqlo::getInstance()->advanced($render);
$cursosAgrupados = Dba::fetchAll($sql);
echo "<pre>";
print_r($cursosAgrupados);

$render->setCondition([
  [ "id","=", $idCursos],
  ["toma_activa","=",true],
]);

$sql = CursoSqlo::getInstance()->advanced($render);
$cursosAprobados = Dba::fetchAll($sql);

$render->setCondition([
  [ "id","=", $idCursos],
  ["toma_activa","=",false],
]);

$sql = CursoSqlo::getInstance()->advanced($render);
$cursosFaltantes = Dba::fetchAll($sql);


//$content = "consolidado/template.html";
//require_once("index/menu.html");
