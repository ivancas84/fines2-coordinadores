<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/dependencias.php");
require_once("function/clasificaciones.php");


$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if (isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Sedes Activas";

$sedes = sedes($fechaAnio, $fechaSemestre, $clasificacion, $dependencia);
if(!empty($sedes)){
    $idSedes = array_column($sedes, "id");
    $render = new Render();
    $render->setCondition([
        ["sede", "=", $idSedes],
        ["baja", "=", false]
    ]);

    $sql = ReferenteSqlo::getInstance()->all(["sede","=",$idSedes]);
    $referentes = Dba::fetchAll($sql);
    $sedesReferentes = array_group_value($referentes, "sede");
}
$content = "sedesActivas/template.html";
require_once("index/menu.html");

function sedes($fechaAnio, $fechaSemestre, $clasificacion, $dependencia){
    $filtros = [
        "fecha_anio" => $fechaAnio,
        "fecha_semestre" => $fechaSemestre,
        "clasificacion_nombre" => $clasificacion,
        "autorizada" => true,
    ];
    
    $render = new Render();
    $render->setCondition([
        ["_filtros", "=", $filtros],
        ["dependencia", "=", $dependencia],
    ]);
    $render->setOrder(["numero" => "ASC"]);
    
    $sql = SedeSqlo::getInstance()->all($render);
    return Dba::fetchAll($sql);
}