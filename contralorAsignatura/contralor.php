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
$fechaEntradaContralor = isset($_GET["fecha_entrada_contralor"]) ? $_GET["fecha_entrada_contralor"] : false; 
$fechaAlta = isset($_GET["fecha_alta"]) ? $_GET["fecha_alta"] : null;
$id = isset($_GET["id"]) ? true : false;
$sql = Data::contralor($fechaAnio, $fechaSemestre, $clasificacion, $fechaEntradaContralor, $fechaAlta);
//echo "<pre>".$sql;
$rows = Dba::fetchAll($sql);
if($id) {
  $ids = array_unique_key($rows, "id");
  echo "<h1>IDS</h1>";
  echo implode(",",$ids);
} 
require_once("contralorAsignatura/contralor.html");
