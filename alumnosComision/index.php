<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");


$comision = isset($_GET["id"]) ? $_GET["id"] : null;
if(empty($comision)) echo "LA COMISION NO EXISTE";

$title = "Alumnos comision";


$render = new Render();
$render->setCondition(["comision", "=", $comision]);
$render->setOrder(["activo" => "DESC", "per_apellidos" => "ASC", "per_nombres" => "ASC"]);

$sqlo = Nomina2Sqlo::getInstance();
$sql = $sqlo->all($render);
$alumnos = Dba::fetchAll($sql);
//$alumnos = $sqlo->valuesAll($alumnos);


if(count($alumnos)){
  $content = "alumnosComision/template.html";
  $data = $sqlo->values($alumnos[0]);
  require_once("index/menu.html");
} else {
  echo "LA COMISION NO POSEE ALUMNOS";
}