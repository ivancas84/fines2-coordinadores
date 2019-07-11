<?
$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];
$clasificacionOptions = ["Fines", "Coordinacion", "Oficios"];
$dbDependencias = Dba::getAll("sede", $_SESSION["dependencia"]);
$dependenciaOptions = array_combine_keys($dbDependencias, "id", "numero");



