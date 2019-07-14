<?php

require_once("../config/config.php");
require_once("config/valuesClasses.php");
require_once("class/model/Dba.php");


$search = isset($_GET["search"]) ? $_GET["search"] : null;
$dependencia = $_SESSION["dependencia"];

$rows = [];

if(!empty($search)) {
  $render = new Render();
  $render->setAdvanced([
    [
      ["pro_nombres","=~",$search, "OR"],
      ["pro_apellidos","=~",$search, "OR"],
      ["pro_numero_documento","=~",$search, "OR"],
      ["pro_cuil","=~",$search, "OR"],
      ["pro_email","=~",$search, "OR"],
    ],
    ["cur_com_dvi_sed_dependencia","=",$dependencia],
    ["profesor","=",true]
  ]);

  
  
  $sql = TomaSqlo::getInstance()->all($render);
  echo "<pre>".$sql;
  $idProfesores = array_column(Dba::fetchAll($sql), "profesor");
  print_r($idProfesores);
  $rows = [];
  if(!empty($idProfesores))
    $sql = IdPersonaSqlo::getInstance()->all(["id", "=", $idProfesores]);
    $rows = Dba::fetchAll($sql);
  }

$title = "Buscar docentes";
$content = "buscarDocentes/template.html";
require_once("index/menu.html"); 
