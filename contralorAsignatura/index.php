<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");


function get_data($toma){
    return [
        "toma" => new TomaValues($toma),
        "profesor" => new IdPersonaValues($toma["profesor_"]),
        "comision" => new ComisionValues($toma["curso_"]["comision_"]),
        "division" => new DivisionValues($toma["curso_"]["comision_"]["division_"]),
        "carga_horaria" => new CargaHorariaValues($toma["curso_"]["carga_horaria_"]),
        "asignatura" => new AsignaturaValues($toma["curso_"]["carga_horaria_"]["asignatura_"]),
        //"horas_catedra" => $toma["horas_catedra"],
    ];
}

$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = "Fines";
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
//$sql = Data::contralorPeriodo($fechaAnio, $fechaSemestre, $clasificacion);
//$rows = Dba::fetchAll($sql);

//$idsProfesores = array_values(array_unique(array_column($rows, "profesor")));

// $render = new Render();
// $render->addAdvanced(["id","=",$idsProfesores]);
// $render->setOrder(["_numero_documento" => "asc"]);
// $profesores_ = Dba::all("id_persona", $render);
// $profesores = array_combine(array_column($profesores_, "id"), $profesores_);


require_once("contralorAsignatura/template.html");

