<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$rows = [];

if(!empty($search)) {
  $render = new Render();
  $render->setSearch($search);
  $render->setAdvanced([
    ["cur_com_dvi_sed_dependencia","=",$dependencia],
    ["profesor","=",true]
  ]);
  
  $idProfesores = Dba::field("toma", "profesor", $render);
  $rows = (!empty($idProfesores)) ? Dba::all("id_persona", ["id", "=", $idProfesores]) : []; 
}

$title = "Buscar docentes";
$content = "buscarDocentes/template.html";
require_once("index/menu.html"); 
