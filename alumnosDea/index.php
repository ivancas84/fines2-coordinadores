<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");

$fechaAnio = (!empty($_GET["fecha_anio"])) ? $_GET["fecha_anio"] : null;
$fechaSemestre = (!empty($_GET["fecha_semestre"])) ? $_GET["fecha_semestre"] : null;
$title = "Alumnos DEA";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$filtros = [
    "dependencia" => $_SESSION["dependencia"],
    "clasificacion" => $_SESSION["clasificacion"],
    "fecha_anio" => $fechaAnio,
    "fecha_semestre" => $fechaSemestre,
    "autorizada" => true,
];

$sql = EntitySqlo::getInstanceFromString("nomina2")->idsActivosFiltros($filtros);
$ids = Dba::fetchAllColumns($sql, 0);

$render = new Render();
$render->setOrder(["com_dvi_sed_numero"=>"asc", "com_anio" => "asc", "com_semestre" => "asc", "com_dvi_numero" => "asc", "per_apellidos" => "asc", "per_nombres" => "asc"]);
$sql = EntitySqlo::getInstanceRequire("nomina2")->getAll($ids, $render);
$alumnos = Dba::fetchAll($sql);
//$alumnos = Dba::getAll("nomina2", $ids, $render);


$content = "alumnosDea/template.html";
require_once("index/index.html");

/*
$render = new Render();
$render->setOrder(["com_dvi_sed_numero"=>"asc", "com_anio" => "asc", "com_semestre" => "asc", "com_dvi_numero" => "asc", "per_apellidos" => "asc", "per_nombres" => "asc"]);

return EntitySql::getInstanceFromString("nomina2")->jsonAll($rows);

require_once("html/nominaDea.html");
*/