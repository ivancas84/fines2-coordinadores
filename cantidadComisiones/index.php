<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
require_once("function/array_combine_keys.php");

require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");
require_once("function/dependencias.php");
require_once("function/clasificaciones.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");



function get_data($row){
  global $total;

  $total += intval($row["_cantidad"]);

  $v["comision"] = new ComisionValues();
  $v["comision"]->fromArray($row);
  $v["cantidad"] = $row["_cantidad"];

  return $v;
}

$title = "Cantidad de comisiones";
$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : ((intval(date("m")) < 7) ? 1 : 2);
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

if(!$fechaAnio || !$fechaSemestre || !$clasificacion) { 
  $content = "_periodoClasificacion/template.html";
  require_once("index/menu.html");
  return;
}

$render = new RenderAux();
$render->setAggregate(["_cantidad"]);
$render->setGroup(["anio","semestre"]);
$render->setCondition([
  ["fecha_anio", "=", $fechaAnio],
  ["fecha_semestre", "=", $fechaSemestre],
  ["dvi_sed_dependencia", "=", $dependencia],
  ["dvi__clasificacion_nombre", "=", $clasificacion],
  ["autorizada","=",true],
]);
$render->setOrder(["tramo"=>"asc"]);

$sql = ComisionSqlo::getInstance()->advanced($render);
$rows = Dba::fetchAll($sql);
$total = 0;

$content = "cantidadComisiones/template.html";
require_once("index/menu.html");
