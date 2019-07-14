<?php

/**
 * componente para obtener la informacion de tomas a partir de cierta fecha de toma
 * este componente fue utilizado para acomodar las tomas debido a que se tuvo que rehacer el contralor ya que las oficinas de contralor así lo requirieron
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");


function get_data($data){
    return [
        "comision" => new ComisionValues($data),
        "division" => new DivisionValues($data["division_"]),
    ];
}

$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : null;
$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : null;
$clasificacion = "Fines";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];
$title = "Turno";

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$render = new Render();
$render->setCondition([
    ["dvi__clasificacion_nombre", "=", $clasificacion],
    ["fecha_anio", "=", $fechaAnio],
    ["fecha_semestre", "=", $fechaSemestre],
]);

$render->setOrder(["dvi_numero" => "ASC", "tramo" => "ASC"]);
$rows = Dba::all("comision", $render);
$content = "turno/template.html";
require_once("index/menu.html");

