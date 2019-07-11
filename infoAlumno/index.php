<?php
require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");


function get_entities($row){
  $v["nomina2"] = new Nomina2Values($row);
  $v["sede"] = new SedeValues($row["comision_"]["division_"]["sede_"]);
  $v["division"] = new DivisionValues($row["comision_"]["division_"]);
  $v["comision"] = new ComisionValues($row["comision_"]);
  return $v;
}



$id = Filter::request("id");
$row = Dba::get("id_persona", $id);
$persona = new IdPersonaValues($row);

$render = new Render();
$render->addAdvanced(["persona", "=", $id]);
$render->setOrder(["com_fecha" => "DESC"]);
$nominas = Dba::all("nomina2", $render);
$periodos = [];

foreach($nominas as $nomina){
  $periodo = $nomina["comision_"]["fecha_anio"]."-".$nomina["comision_"]["fecha_semestre"];
  if (!array_key_exists($periodo, $periodos)) $periodos[$periodo] = [];
  array_push($periodos[$periodo], $nomina);
}

$content = "infoAlumno/template.html";
$title = "Datos de Alumno";

require_once("index/menu.html");
