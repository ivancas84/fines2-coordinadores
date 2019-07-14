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



function get_data($row){
  global $total;

  $total += intval($row["_cantidad"]);

  $v["comision"] = new ComisionValues($row);
  $v["cantidad"] = $row["_cantidad"];

  return $v;
}
/*
$id = Filter::request("id");
$persona_ = Dba::get("id_persona", $id);
$dependencia = $_SESSION["dependencia"];

$render = new Render();
$render->addAdvanced([["profesor", "=", $id], ["cur_com_dvi_sed_dependencia", "=", $dependencia]]);
$render->setOrder(["cur_com_fecha_anio" => "DESC", "cur_com_fecha_semestre" => "DESC"]);
$tomas = Dba::all("toma", $render);

$content = "cantidadComisiones/template.html";
$title = "Cantidad de Comisiones";
//$persona = new IdPersonaValues($persona_);
*/


$title = "Cantidad de comisiones";
$dependencia = $_SESSION["dependencia"];
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : null;
require_once("_periodoClasificacion/options.php");


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
