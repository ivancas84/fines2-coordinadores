<?php

require_once("../config/config.php");
require_once("class/model/Values.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");
require_once("function/array_group_value.php");


$id = Filter::request("id");

$sql = EntitySqlo::getInstanceRequire("id_persona")->getAll([$id]);
$persona =  EntitySqlo::getInstanceRequire("id_persona", Dba::fetchAssoc($sql));
//$dependencia = $_SESSION["dependencia"];

$render = new Render();
$render->setCondition([["profesor", "=", $id]]);
$render->setOrder(["cur_com_fecha_anio" => "DESC", "cur_com_fecha_semestre" => "DESC"]);
$sql = EntitySqlo::getInstanceRequire("toma")->all($render);
$periodos = array_group_value(Dba::fetchAll($sql), "cur_com_periodo");


$content = "infoDocente/template.html";
$title = "Datos de Docente";
require_once("index/menu.html");
