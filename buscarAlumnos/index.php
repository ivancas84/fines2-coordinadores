<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("function/array_unique_key.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$alumnos = [];

if(!empty($search)) {
  $idAlumnos = id_alumnos($search, $dependencia);
  $alumnos = alumnos($idAlumnos);
}

$title = "Buscar alumnos";
$content = "buscarAlumnos/template.html";
require_once("index/menu.html"); 

function id_alumnos($search, $dependencia){
  $render = new Render();
  $render->setCondition([
    ["per_search_","=~", $search],
    ["com_dvi_sed_dependencia","=",$dependencia],
    ["persona","=",true]
  ]);

  $sql = Nomina2Sqlo::getInstance()->all($render);
  return array_unique_key(Dba::fetchAll($sql), "persona");
}

function alumnos($idAlumnos){
  if(empty($idAlumnos)) return [];
  $render = new Render();
  $render->setCondition(["id","=", $idAlumnos]);
  $render->setOrder(["apellidos" => "ASC", "nombres" => "ASC"]);
  $sql = IdPersonaSqlo::getInstance()->all($render);
  return Dba::fetchAll($sql);
}