<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Render.php");
require_once("config/valuesClasses.php");
require_once("function/array_combine_keys.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/clasificaciones.php");
require_once("function/dependencias.php");
require_once("function/ordenes.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : ((intval(date("m")) < 7) ? 1 : 2);
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
$orden = isset($_GET["orden"]) ? $_GET["orden"] : "Tramo" ;

$render = new Render();
$render->setAdvanced(
    [
        ["fecha_anio", "=", $fechaAnio],
        ["fecha_semestre", "=", $fechaSemestre],
        ['autorizada', '=', true],
        ["dvi__clasificacion_nombre", "=", $clasificacion],
        ["dvi_sed_dependencia", "=", $dependencia],
    ]
);

switch ($orden){
    case "Tramo": $render->setOrder(["anio"=>"asc", "semestre"=>"asc", "dvi_sed_numero"=>"asc"]); break;
    case "Sede": $render->setOrder(["dvi_sed_numero"=>"asc", "anio"=>"asc", "semestre"=>"asc"]); break;
    case "Apertura": $render->setOrder(["apertura"=> "asc", "anio"=>"asc", "semestre"=>"asc","dvi_sed_numero"=>"asc"]); break;
    case "Dependencia": $render->setOrder(["dependencia"=> "ASC", "dvi_sed_numero"=>"asc", "anio"=>"asc", "semestre"=>"asc"]); break;
    case "Coordinador": $render->setOrder(["dvi_sed_coo_nombres"=>"ASC", "dvi_sed_numero"=>"asc", "anio"=>"asc", "semestre"=>"asc"]); break;
}
$comisiones = Dba::all("comision",$render);
  
  
  
$content = "comisionesAutorizadas/template.html";
$title = "Comisiones Autorizadas";
require_once("index/menu.html");


