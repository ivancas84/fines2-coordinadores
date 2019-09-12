<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/array_unique_key.php");
require_once("function/array_combine_key.php");



$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Docentes DEA";


$filtros = [
    ["cur_com_dvi_sed_dependencia", "=", $_SESSION["dependencia"]],
    ["cur_com_dvi__clasificacion", "=", $_SESSION["clasificacion"]],
    ["cur_com_fecha_anio", "=", $fechaAnio],
    ["cur_com_fecha_semestre", "=", $fechaSemestre],
    ["cur_com_autorizada", "=", true],
    ["profesor", "=", true]
];

$render = new RenderAux();
$render->setCondition($filtros);
$render->setAggregate(["cur_ch_sum_horas_catedra"]);
$render->setGroup(["profesor"]);
$render->setOrder(["pro_apellidos"=>"asc", "pro_nombres"=>"asc"]);

$sql = EntitySqlo::getInstanceRequire("toma")->advanced($render);
echo "<pre>".$sql;
$horas = Dba::fetchAll($sql);
$idsProfesores = array_unique_key($horas ,"profesor");
$profesores_ = Dba::getAll("id_persona", $idsProfesores);

$profesores = array_combine_key($profesores_, "id");


function get_data($d){
    global $profesores;

    return [
      "persona" => new IdPersonaValues($profesores[$d["profesor"]]),
      "suma_horas_catedra" => $d["suma_horas_catedra"],      
    ];
}

//$content = "docentesDea/template.html";
//require_once("index/index.html");
