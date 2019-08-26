<?php
require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");


$fechaAnio = $_GET["fecha_anio"];
$fechaSemestre = $_GET["fecha_semestre"];

$render = new Render();
$render->setOrder(["per_apellidos"=>"asc", "per_nombres" => "asc", "per_numero_documento" => "asc"]);
$sql = EntitySqlo::getInstanceString("nomina2")->repetidosPeriodoAll($fechaAnio, $fechaSemestre, $render);

$rows =  Dba::fetchAll($sql);
$alumnos = EntitySqlo::getInstanceString("nomina2")->jsonAll($rows);

$content = "./template.html";
$title = "Alumnos Repetidos";
require_once("index/menu.html");