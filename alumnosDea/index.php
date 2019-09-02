<?php
require_once("../config/config.php");

require_once("class/model/Dba.php");
require_once("class/model/Sqlo.php");

require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");
require_once("function/fecha_anios.php");
require_once("function/fecha_semestres.php");
require_once("function/clasificaciones.php");
require_once("function/dependencias.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$title = "Alumnos DEA";

$personasActivasRepetidas = personas_activas_repetidas($fechaAnio, $fechaSemestre);
$alumnos = nominas_activas($personasActivasRepetidas, $clasificacion, $fechaAnio, $fechaSemestre, $_SESSION["dependencia"]);

require_once(PATH_ROOT_SITE . "alumnosDea/template.html");

function personas_activas_repetidas($fechaAnio, $fechaSemestre){
    $sql = EntitySqlo::getInstanceRequire("nomina2")->personasActivasRepetidasPeriodo(
        ["fecha_anio" => $fechaAnio, "fecha_semestre" => $fechaSemestre]
    );

    return Dba::fetchAllColumns($sql, 0);
}

function nominas_activas($personasActivas, $clasificacion, $fechaAnio, $fechaSemestre, $dependencia){
    $render = new Render();
    $render->setCondition([
        ["persona","!=",$personasActivas],
        ["com_dvi__clasificacion_nombre","=",$clasificacion],
        ["com_fecha_anio","=",$fechaAnio],
        ["com_fecha_semestre","=",$fechaSemestre],
        ["com_autorizada","=",true],
        ["com_dvi_sed_dependencia","=",$dependencia],
        ["activo","=",true]
    ]);
    $render->setOrder(
        ["com_dvi_sed_numero"=>"asc", "com_anio" => "asc", "com_semestre" => "asc", "com_dvi_numero" => "asc", "per_apellidos" => "asc", "per_nombres" => "asc"]
    );
    $sql = EntitySqlo::getInstanceRequire("nomina2")->all($render);
    //echo "<pre>".$sql;
    return Dba::fetchAll($sql);    
}


/*
function 
$render = new Render();
$render->setOrder(["com_dvi_sed_numero"=>"asc", "com_anio" => "asc", "com_semestre" => "asc", "com_dvi_numero" => "asc", "per_apellidos" => "asc", "per_nombres" => "asc"]);
$sql = EntitySqlo::getInstanceRequire("nomina2")->getAll($ids, $render);
$alumnos = Dba::fetchAll($sql);

print_r($alumnos);
//$alumnos = Dba::getAll("nomina2", $ids, $render);


$content = "alumnosDea/template.html";
require_once("index/index.html");

/*
$render = new Render();
$render->setOrder(["com_dvi_sed_numero"=>"asc", "com_anio" => "asc", "com_semestre" => "asc", "com_dvi_numero" => "asc", "per_apellidos" => "asc", "per_nombres" => "asc"]);

return EntitySql::getInstanceString("nomina2")->jsonAll($rows);

require_once("html/nominaDea.html");
*/