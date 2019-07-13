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
require_once("function/clasificaciones.php");
require_once("function/dependencias.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Control Notas";

$cursos = cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion);
$comisiones = array_group_value($cursos, "comision");
$content = "controlNotas/tabla.html";
require_once("index/index.html");


function cursos($fechaAnio, $fechaSemestre, $dependencia, $clasificacion){
  $filtros = [ //filtros para cursos
    ["com_fecha_anio", "=", $fechaAnio],
    ["com_fecha_semestre", "=", $fechaSemestre],
    ["com_autorizada", "=", true],
    ["com_dvi_sed_dependencia", "=", $dependencia],
    ["com_dvi__clasificacion_nombre", "=", $clasificacion]
  ];

  $render = new Render();
  $render->setAdvanced($filtros);
  $render->setOrder([
    "com_dvi_sed_numero" => "ASC", 
    "com_anio" => "ASC", 
    "com_semestre" => "ASC",
    "ch_asi_nombre" => "ASC"
  ]);

  $sql = CursoSqlo::getInstance()->all($render);
  return Dba::fetchAll($sql);
}




