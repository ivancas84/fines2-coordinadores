<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
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

$title = "Cantidad de comisiones por coordinador";

$dependencia = $_SESSION["dependencia"];

$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : null;
$coordinador = $_GET["coordinador"];

$render = new RenderAux();
$render->setAggregate(["_cantidad"]);
$render->setGroup(["anio","semestre","dvi_sed_coordinador"]);
$render->setAdvanced([
  ["fecha_anio", "=", $fechaAnio],
  ["fecha_semestre", "=", $fechaSemestre],
  ["dvi_sed_dependencia", "=", $dependencia],
  ["dvi__clasificacion_nombre", "=", $clasificacion],
  ["autorizada","=",true],
  ["dvi_sed_coordinador", "=", $coordinador],

]);
$render->setOrder(["tramo"=>"asc"]);

$sql = ComisionSqlo::getInstance()->advanced($render);
$rows = Dba::fetchAll($sql);
$personaData = Dba::get("id_persona", $coordinador);
$persona= new IdPersonaValues($personaData);
$total = 0;

$content = "cantidadComisionesCoordinadorTramo/template.html";
require_once("index/menu.html");
