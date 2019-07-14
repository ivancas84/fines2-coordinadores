<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");
require_once("function/array_unique_key.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$rows = [];

if(!empty($search)) {
  $render = new Render();
  $render->setCondition([
    ["pro_search_", "=~", $search ],
    ["cur_com_dvi_sed_dependencia","=",$dependencia],
    ["profesor","=",true]
  ]);

  
  $sql = TomaSqlo::getInstance()->all($render);
  $idProfesores = array_unique_key(Dba::fetchAll($sql), "profesor");
  $rows = [];
  if(!empty($idProfesores))
    $sql = IdPersonaSqlo::getInstance()->all(["id", "=", $idProfesores]);
    $rows = Dba::fetchAll($sql);
  }

$title = "Buscar docentes";
$content = "buscarDocentes/template.html";
require_once("index/menu.html"); 
