<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../../config/config.php");
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
$sql = Data::contralorRenunciasPendientes($fechaAnio, $fechaSemestre);
$rows = Dba::fetchAll($sql);

foreach($rows as $row){
  $v = EntitySqlo::getInstanceRequire("toma")->values($row);
  echo "<p>".$v["sede"]->numero().$v["division"]->serie()." ".$v["asignatura"]->nombre()." " .$v["toma"]->id()." ".$v["profesor"]->nombre(). " ".$v["toma"]->fechaToma("Y-m-d") . " " . $v["toma"]->alta("Y-m-d") . "</p>";
}

