<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/dependencias.php");
require_once("function/clasificaciones.php");
require_once("function/fechaAnios.php");
require_once("function/fechaSemestres.php");
require_once("function/id_dependencia_numero.php");



$title = "Email docentes";

$dependencia = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : fecha_semestre();
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia_ = ($dependencia == "Todos") ?  $_SESSION["dependencia"] : id_dependencia_numero($dependencia);


$filtros = [
  ["cur_com_fecha_anio", "=", $fechaAnio],
  ["cur_com_fecha_semestre", "=", $fechaSemestre],
  ["cur_com_dvi_sed_dependencia", "=", $dependencia_],
  ["cur_com_dvi_sed__clasificacion_nombre", "=", $clasificacion],
  ["pro_email","=",true]
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["pro_email"=>"asc"]);
$sql = TomaSqlo::getInstance()->all($render);
$emails = array_unique_key(Dba::fetchAll($sql), "pro_email");

$content = "emailDocentes/template.html";
require_once("index/menu.html");

function fecha_semestre(){
  return (date("m") < 7) ? "1" : "2";
}