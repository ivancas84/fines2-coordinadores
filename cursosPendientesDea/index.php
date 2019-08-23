<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/array_combine_key.php");


$dependencia = $_SESSION["dependencia"];

$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : null;
require_once("_periodoClasificacion/options.php");


if(!$fechaAnio || !$fechaSemestre || !$clasificacion) { 
  $content = "_periodoClasificacion/template.html";
  require_once("index/menu.html");
  return;
}

$filters = [
  ["com_fecha_anio", "=", $fechaAnio],
  ["com_fecha_semestre", "=", $fechaSemestre],
  ["com_dvi_sed_dependencia", "=", $dependencia],
  ["com_dvi__clasificacion_nombre", "=", $clasificacion],
  ["toma_activa","=",false],
  ["com_autorizada","=",true]
];

$render = new Render();
$render->addAdvanced($filters);
$render->setOrder(["ch_asi_nombre" => true, "com_dvi_sed_numero"=> true, "com_anio"=> true, "com_semestre"=> true]);
$sql = EntitySqlo::getInstanceRequire("curso")->select(
  "count(curs.id) as cursos, sum(ch.horas_catedra) as suma_horas_catedra", 
  $render
);


$row = Dba::fetchAll($sql);
//echo $sql;
echo "<pre>";
print_r($row);
