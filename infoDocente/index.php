<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("class/Filter.php");
require_once("function/formatDate.php");
require_once("class/SpanishDateTime.php");
require_once("class/model/values/idPersona/IdPersona.php");
require_once("class/model/Data.php");
require_once("function/array_group_value.php");


$id = Filter::request("id");

$sql = IdPersonaSqlo::getInstance()->getAll([$id]);
$persona =  IdPersonaValues::getInstanceFromArray(Dba::fetchAssoc($sql));
$dependencia = $_SESSION["dependencia"];

$render = new Render();
$render->setCondition([["profesor", "=", $id], ["cur_com_dvi_sed_dependencia", "=", $dependencia]]);
$render->setOrder(["cur_com_fecha_anio" => "DESC", "cur_com_fecha_semestre" => "DESC"]);
$sql = TomaSqlo::getInstance()->all($render);
$periodos = array_group_value(Dba::fetchAll($sql), "cur_com_periodo");


$content = "infoDocente/template.html";
$title = "Datos de Docente";
require_once("index/menu.html");
