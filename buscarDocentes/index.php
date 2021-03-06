<?php

require_once("../config/config.php");
require_once("class/model/Values.php");
require_once("class/controller/Dba.php");
require_once("function/array_unique_key.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$rows = [];

if(!empty($search)) {
  $idProfesores = id_profesores($search, $dependencia);
  if(!empty($idProfesores)) $rows = profesores($idProfesores);
}

$title = "Buscar docentes";
$content = "buscarDocentes/template.html";
require_once("index/menu.html"); 


function id_profesores($search, $dependencia){
  $render = new Render();
  $render->setCondition([
    ["pro__search", "=~", $search ],
    ["cur_com_dvi_sed_dependencia","=",$dependencia],
    ["profesor","=",true]
  ]);

  $sql = EntitySqlo::getInstanceRequire("toma")->all($render);
  return array_unique_key(Dba::fetchAll($sql), "profesor");
}

function profesores($idProfesores){
  if (empty($idProfesores)) return [];
  $render = new Render();
  $render->setCondition(["id", "=", $idProfesores]);
  $render->setOrder(["apellidos" => "ASC", "nombres" => "ASC"]);
  $sql = EntitySqlo::getInstanceRequire("id_persona")::getInstance()->all($render);
  return Dba::fetchAll($sql);
}