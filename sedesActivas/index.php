<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");


$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
require_once("_periodoClasificacion/options.php");

$title = "Sedes Activas";

$render = new Render();
$render->setAdvanced([
    ["_fecha_anio", "=", $fechaAnio],
    ["_fecha_semestre", "=", $fechaSemestre],
    ["_autorizada", "=", true],
    ["dependencia", "=", $dependencia],
    ["_clasificacion_nombre", "=", $clasificacion]
]);
$render->setOrder(["numero" => "ASC"]);

$sql = SedeSqlo::getInstance()->all($render);
$sedes = Dba::fetchAll($sql);

$idSedes = array_unique_key($sedes, "id");

$render = new Render();
$render->setAdvanced([
    ["sede", "=", $idSedes],
    ["baja", "=", false]
]);

$sql = ReferenteSqlo::getInstance()->all(["sede","=",$idSedes]);
$referentes = Dba::fetchAll($sql);
$sedesReferentes = array_group_value($referentes, "sede");

$content = "sedesActivas/template.html";
require_once("index/menu.html");
