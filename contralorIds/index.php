<?php

/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");


$render = [
    [
        ["estado","=","Aprobada"],
        ["estado","=","Pendiente","OR"],
        ["estado","=","Baja","OR"],       

    ],
    ["fecha_entrada_contralor","=",false],
    ["estado_contralor","=","Pasar"],
    ["cur_com_fecha_anio","=",$fechaAnio],
    ["cur_com_fecha_semestre","=",$fechaSemestre],
    ["cur_com_dvi__clasificacion_nombre",$clasificacion]
];

$ids = Dba::ids($render);
echo implode(", ", $ids);