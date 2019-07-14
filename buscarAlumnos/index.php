<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$alumnos = [];

if(!empty($search)) {
  $render = new Render();
  $render->setSearch($search);
  $render->setCondition([
    ["com_dvi_sed_dependencia","=",$dependencia],
    ["persona","=",true]
  ]);

  
  $idAlumnos = Dba::field("nomina2", "persona", $render);

  $rows = (!empty($idAlumnos)) ? Dba::all("id_persona", ["id", "=", $idAlumnos]) : []; 
}

$title = "Buscar alumnos";
$content = "buscarAlumnos/template.html";
require_once("index/menu.html"); 
