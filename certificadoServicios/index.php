<?php

require_once("../config/config.php");
require_once("class/model/Values.php");
require_once("class/controller/Dba.php");
require_once("class/tools/Filter.php");
require_once("function/formatDate.php");
require_once("class/tools/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");

/*function get_data($toma){

  $v["toma"] = new TomaValues($toma);
  $v["sede"] = new SedeValues($toma["curso_"]["comision_"]["division_"]["sede_"]);
  $v["division"] = new DivisionValues($toma["curso_"]["comision_"]["division_"]);
  $v["comision"] = new ComisionValues($toma["curso_"]["comision_"]);
  $v["curso"] = new CursoValues($toma["curso_"]);
  $v["asignatura"] = new AsignaturaValues($toma["curso_"]["carga_horaria_"]["asignatura_"]);
  $v["carga_horaria"] = new CargaHorariaValues($toma["curso_"]["carga_horaria_"]);
  return $v;
}*/

$id = Filter::request("id");
$persona_ = Dba::get("id_persona", $id);
$dependencia = $_SESSION["dependencia"];

$render = new Render();
$render->addAdvanced([["profesor", "=", $id], ["cur_com_dvi_sed_dependencia", "=", $dependencia]]);
$render->setOrder(["cur_com_fecha_anio" => "DESC", "cur_com_fecha_semestre" => "DESC"]);
$tomas = Dba::all("toma", $render);

$content = "certificadoServicios/template.html";
$title = "Certificado de Servicios";
$persona = new IdPersonaValues($persona_);
require_once("index/index.html");
