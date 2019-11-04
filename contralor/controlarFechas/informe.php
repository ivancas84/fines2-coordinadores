<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");

$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
if(empty($fechaInicio = $_GET["fecha_inicio"])) die("Fecha inicio no definida");
if(empty($fechaFin = $_GET["fecha_fin"])) die("Fecha fin no definida");

$sql = Data::contralorControlFechaAprobada($fechaAnio, $fechaSemestre, $clasificacion, $fechaInicio, $fechaFin);
$rows = Dba::fetchAll($sql);

require_once("./informe.html");
