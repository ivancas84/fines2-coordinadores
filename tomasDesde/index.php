<?php

/**
 * componente para obtener la informacion de tomas a partir de cierta fecha de toma
 * este componente fue utilizado para acomodar las tomas debido a que se tuvo que rehacer el contralor ya que las oficinas de contralor así lo requirieron
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");


function get_data($toma){
    return [
        "curso" => new CursoValues($toma["curso_"]),
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
$fechaTomaDesde = isset($_GET["fecha_toma_desde"]) ? $_GET["fecha_toma_desde"] : null;

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];
$title = "Tomas desde";

if(!$fechaAnio || !$fechaSemestre || !$fechaTomaDesde) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "tomasDesde/_formulario.html";
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
    ["fecha_toma",">=",$fechaTomaDesde],
]);

$render->setOrder(["cur_id" => "ASC"]);
$rows = Dba::all("toma", $render);
$content = "tomasDesde/template.html";
require_once("index/menu.html");

