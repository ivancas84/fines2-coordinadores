<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("function/array_unique_key.php");
require_once("function/array_combine_key.php");
require_once("function/clasificaciones.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/dependencias.php");

$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
$fechaEntradaContralor = isset($_GET["fecha_entrada_contralor"]) ? $_GET["fecha_entrada_contralor"] : false; 

$alumnosCantidad = Data::alumnosActivosRepetidosFiltros($fechaAnio, $fechaSemestre, $clasificacion, $dependencia);
if(empty($alumnosCantidad)) die("No hay alumnos duplicados");

$idPersonas = array_unique_key($alumnosCantidad, "persona");
$idPersonasCantidad = array_combine_key($alumnosCantidad, "persona");

$sql = Data::nominaFiltrosPersonas($fechaAnio, $fechaSemestre, $clasificacion, $dependencia, $idPersonas);
$nominas = Dba::fetchAll($sql);

$title = "Alumnos repetidos";
$content = "alumnosRepetidos/alumnosRepetidos.html";
require_once("index/menu.html"); 