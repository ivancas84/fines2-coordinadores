<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");




$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;
$fechaEntradaContralor = isset($_GET["fecha_entrada_contralor"]) ? $_GET["fecha_entrada_contralor"] : false; 

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "contralorAsignatura/formulario.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$render = new Render();
$render->setCondition([
    ["cur_com_dvi__clasificacion_nombre", "=", $clasificacion],
    ["cur_com_fecha_anio", "=", $fechaAnio],
    ["cur_com_fecha_semestre", "=", $fechaSemestre],
    [
        ["estado","=","Aprobada"],
        ["estado","=","Renuncia","OR"],
        ["estado","=","Baja","OR"],
    ],
    ["profesor","=",true],
    ["fecha_entrada_contralor","=",$fechaEntradaContralor],
    ["estado_contralor","=","Pasar"]
]);
$render->setOrder(["pro__numero_documento" => "ASC"]);
$sql = EntitySqlo::getInstanceRequire("toma")->all($render);
$rows = Dba::fetchAll($sql);


require_once("contralorAsignatura/contralor.html");

