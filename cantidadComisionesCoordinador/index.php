<?php

require_once("../config/config.php");
require_once("class/model/Values.php");
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
  $v["persona"] = new IdPersonaValues();
  if($row["dvi_sed_coordinador"]) {
    $sql = EntitySqlo::getInstanceRequire("id_persona")::getInstance()->getAll([$row["dvi_sed_coordinador"]]);
    $v["persona"]->fromArray(Dba::fetchAssoc($sql));
  
  }
  $v["cantidad"] = $row["_cantidad"];

  return $v;
}

$title = "Cantidad de comisiones por coordinador";

$title = "Cantidad de comisiones";
$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : ((intval(date("m")) < 7) ? 1 : 2);
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$render = new RenderAux();
$render->setAggregate(["_cantidad"]);
$render->setGroup(["dvi_sed_coordinador"]);
$render->setCondition([
  ["fecha_anio", "=", $fechaAnio],
  ["fecha_semestre", "=", $fechaSemestre],
  ["dvi_sed_dependencia", "=", $dependencia],
  ["dvi__clasificacion_nombre", "=", $clasificacion],
  ["autorizada","=",true],
]);
$render->setOrder(["dvi_sed_coo_nombres"=>"asc"]);

$sql = EntitySqlo::getInstanceRequire("comision")->advanced($render);
$rows = Dba::fetchAll($sql);
$total = 0;

$content = "cantidadComisionesCoordinador/template.html";
require_once("index/menu.html");
