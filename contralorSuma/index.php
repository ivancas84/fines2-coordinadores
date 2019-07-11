<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");


function get_data($toma){
    global $profesores;

    $idp = $toma["profesor"];
    return [
        "toma" => new TomaValues($toma),
        "profesor" => new IdPersonaValues($profesores[$idp]),
        "horas_catedra" => $toma["horas_catedra"],
    ];
}

$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = "Fines";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$sql = Data::contralorPeriodo($fechaAnio, $fechaSemestre, $clasificacion);
$rows = Dba::fetchAll($sql);

$idsProfesores = array_values(array_unique(array_column($rows, "profesor")));

$render = new Render();
$render->addAdvanced(["id","=",$idsProfesores]);
$render->setOrder(["_numero_documento" => "asc"]);
$profesores_ = Dba::all("id_persona", $render);
$profesores = array_combine(array_column($profesores_, "id"), $profesores_);

require_once("contralor/template.html");

