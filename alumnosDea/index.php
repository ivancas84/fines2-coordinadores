<?php
require_once("../config/config.php");

require_once("class/model/Dba.php");
require_once("class/model/Sqlo.php");
require_once("class/model/Data.php");

require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/clasificaciones.php");
require_once("function/dependencias.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Alumnos DEA";

$sql = Data::alumnosActivosTodos1RepetidosFiltros($fechaAnio, $fechaSemestre, $clasificacion, $dependencia);
$alumnosRepetidos = Dba::fetchAllColumns($sql, 0);

$sql = Data::nominaActivosTodos1FiltrosSinPersonas($fechaAnio, $fechaSemestre, $clasificacion, $dependencia, $alumnosRepetidos);
$alumnos = Dba::fetchAll($sql);

require_once(PATH_ROOT_SITE . "alumnosDea/template.html");


