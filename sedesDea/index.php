<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");

function get_data($row){
    return [
        "sede" => new SedeValues($row),
        "domicilio" => new DomicilioValues($row["domicilio_"]),
    ];
}




$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];
$dependencia = $_SESSION["dependencia"];
$fechaAnio = (!empty($_GET["fecha_anio"])) ? $_GET["fecha_anio"] : null;
$fechaSemestre = (!empty($_GET["fecha_semestre"])) ? $_GET["fecha_semestre"] : null;
//$dependencia =  $_REQUEST["dependencia"];

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "sedesDea.php";
    $title = "Sedes Dea";
    require_once("index/menu.html");
    return;
}

//$idsPlanes = Dba::field("clasificacion_plan", "plan", [["clasificacion", "=", $display["aux"]["clasificacion"]]]);
//$dependencia = "1532435439156616";

$idsPlanes = [];

$render = new Render();

$idsPlanes = Dba::field("clasificacion_plan", "plan", [["cla_nombre", "=", "Fines"]]);

$render->setCondition([
    [
        "_filtros",
        "=",
        [
            "fecha_anio" => $fechaAnio,
            "fecha_semestre" => $fechaSemestre,
            "dependencia" => $dependencia,
            "plan" => $idsPlanes,
            "autorizada" => true
        ],
    ],
]);

$render->setOrder(["nombre" => "ASC"]);

$sedes = Dba::all("sede", $render);
//$dependencias = Dba::getAll("sede",$dependencia);

$content = "sedesDea/template.html";
$title = "Sedes DEA";
require_once("index/index.html");


