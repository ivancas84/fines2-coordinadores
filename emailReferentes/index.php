<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");

$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
require_once("_periodoClasificacion/options.php");

$title = "Email Referentes";

$filtros = [
  ["sed__fecha_anio", "=", $fechaAnio],
  ["sed__fecha_semestre", "=", $fechaSemestre],
  ["sed_dependencia", "=", $dependencia],
  ["sed__clasificacion_nombre", "=", $clasificacion],
  ["sed__autorizada", "=", true],
  ["per_email","=",true]
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["per_email"=>"asc"]);
$sql = EntitySqlo::getInstanceRequire("referente")::getInstance()->all($render);
$emails = array_unique_key(Dba::fetchAll($sql), "per_email");


$content = "emailReferentes/template.html";
require_once("index/menu.html");
