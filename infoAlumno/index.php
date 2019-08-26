<?php
require_once("../config/config.php");
require_once("class/model/Values.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/array_group_value.php");
require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");


$id = Filter::request("id");
$sql = EntitySqlo::getInstanceRequire("id_persona")::getInstance()->getAll([$id]);
$persona = EntityValues::getInstanceRequire("id_persona", Dba::fetchAssoc($sql));
$periodos = periodos($id);

$content = "infoAlumno/template.html";
$title = "Datos de Alumno";

require_once("index/menu.html");



function periodos($idPersona){ 
  $render = new Render();
  $render->setCondition(["persona", "=", $idPersona]);
  $render->setOrder(["com_fecha" => "DESC"]);
  $sql = EntitySqlo::getInstanceRequire("nomina2")->all($render);
  return array_group_value(Dba::fetchAll($sql), "com_periodo");
}
