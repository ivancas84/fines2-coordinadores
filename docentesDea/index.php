<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/array_combine_key.php");


function get_data($d){
    global $profesores;

    return [
      "persona" => new IdPersonaValues($profesores[$d["profesor"]]),
      "suma_horas_catedra" => $d["suma_horas_catedra"],      
    ];
}

$fechaAnio = (!empty($_GET["fecha_anio"])) ? $_GET["fecha_anio"] : null;
$fechaSemestre = (!empty($_GET["fecha_semestre"])) ? $_GET["fecha_semestre"] : null;
$title = "Docentes DEA";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$filtros = [
    ["cur_com_dvi_sed_dependencia", "=", $_SESSION["dependencia"]],
    ["cur_com_dvi__clasificacion", "=", $_SESSION["clasificacion"]],
    ["cur_com_fecha_anio", "=", $fechaAnio],
    ["cur_com_fecha_semestre", "=", $fechaSemestre],
    ["cur_com_autorizada", "=", true],
    ["profesor", "=", true]
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["pro_apellidos"=>"asc", "pro_nombres"=>"asc"]);

$sql = EntitySqlo::getInstanceRequire("toma")->profesorSumaHorasCatedraAll($render);
$horas = Dba::fetchAll($sql);
$idsProfesores = array_values(array_unique(array_column ($horas ,"profesor")));
$profesores_ = Dba::getAll("id_persona", $idsProfesores);

$profesores = array_combine_key($profesores_, "id");

$content = "docentesDea/template.html";
require_once("index/index.html");
