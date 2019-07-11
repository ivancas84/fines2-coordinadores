<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");

function toma_values($toma){
  $v["toma"] = new TomaValues($toma);
  $v["sede"] = new SedeValues($toma["curso_"]["comision_"]["division_"]["sede_"]);
  $v["division"] = new DivisionValues($toma["curso_"]["comision_"]["division_"]);
  $v["comision"] = new ComisionValues($toma["curso_"]["comision_"]);
  $v["curso"] = new CursoValues($toma["curso_"]);
  $v["asignatura"] = new AsignaturaValues($toma["curso_"]["carga_horaria_"]["asignatura_"]);
  $v["carga_horaria"] = new CargaHorariaValues($toma["curso_"]["carga_horaria_"]);
  return $v;
}

$id = Filter::request("id");
$persona_ = Dba::get("id_persona", $id);
$dependencia = $_SESSION["dependencia"];

$render = new Render();
$render->addAdvanced([["profesor", "=", $id], ["cur_com_dvi_sed_dependencia", "=", $dependencia]]);
$render->setOrder(["cur_com_fecha_anio" => "DESC", "cur_com_fecha_semestre" => "DESC"]);
$tomas = Dba::all("toma", $render);
$periodos = [];



foreach($tomas as $toma){
  $periodo = $toma["curso_"]["comision_"]["fecha_anio"]."-".$toma["curso_"]["comision_"]["fecha_semestre"];
  if (!array_key_exists($periodo, $periodos)) $periodos[$periodo] = [];
  array_push($periodos[$periodo], $toma);
}

$content = "infoDocente/template.html";
$title = "Datos de Docente";
$persona = new IdPersonaValues($persona_);
require_once("index/menu.html");
