<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("function/array_combine_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_unique_key.php");
require_once("function/array_group_value.php");


// $comision = isset($_GET["id"]) ? $_GET["id"] : null;
// if(empty($comision)) echo "LA COMISION NO EXISTE";

$fechaSemestre = isset($_GET["fecha_semestre"]) ? $_GET["fecha_semestre"] : 2 ;

$render = new Render();
$render->setCondition([
  ["com_fecha_anio", "=", "2019"],
  ["com_fecha_semestre", "=", $fechaSemestre],
  ["com_autorizada", "=", true],
  ["activo", "=", true],
]);
$render->setOrder(["com_dvi_sed_numero"=>"ASC", "com_dvi_serie" =>"ASC", "com_anio"=>"ASC", "com_semestre"=>"ASC"]);

$sqlo = EntitySqlo::getInstanceRequire("nomina2");
$sql = $sqlo->all($render);
$alumnos = Dba::fetchAll($sql);

$comisionAlumnos = array_group_value($alumnos, "comision");

$comisiones = [];


$totalPorOrientacion = [];
$totalAlumnos = 0;
$totalVarones = 0;

foreach($comisionAlumnos as $ca) {
  $c = [];
  $c["total"] = count($ca);
  $c["anio"] = $ca[0]["com_anio"];
  $c["turno"] = $ca[0]["com_dvi_turno"];

  
  $c["nombre_division"] =  $ca[0]["com_dvi_sed_numero"].$ca[0]["com_dvi_serie"].$ca[0]["com_anio"].$ca[0]["com_semestre"];
  $c["orientacion"] = $ca[0]["com_dvi_pla_orientacion"];
  
  $varones = 0;
  $menos14 = 0;
  $e14 = 0;
  $e15 = 0;
  $e16 = 0;
  $e17 = 0;
  $e18 = 0;
  $e19 = 0;
  $e20 = 0;
  $e21 = 0;
  $e22 = 0;
  $e23 = 0;
  $e24 = 0;
  $e25a29 = 0;
  $e30a34 = 0;
  $e35a39 = 0;
  $e40a44 = 0;
  $e45a49 = 0;
  $e50a54 = 0;
  $e55mas = 0;
  $sinEdad = 0;

  if(!key_exists($c["orientacion"], $totalPorOrientacion)) $totalPorOrientacion[$c["orientacion"]] = 0;
  
  foreach($ca as $alumno){

    if($alumno["per_genero"] == "Masculino") $varones++;
    $birthdate = DateTime::createFromFormat("Y-m-d", $alumno["per_fecha_nacimiento"]);
    if($birthdate) {
      $today = new DateTime("today");
    
      $y = $birthdate->diff($today)->y;
      if($y < 14) $menos14++;
      if($y == 14) $e14++;
      if($y == 15) $e15++;
      if($y == 16) $e16++;
      if($y == 17) $e17++;
      if($y == 18) $e18++;
      if($y == 19) $e19++;
      if($y == 20) $e20++;
      if($y == 21) $e21++;
      if($y == 22) $e22++;
      if($y == 23) $e23++;
      if($y == 24) $e24++;
      if($y >= 25 && $y <= 29) $e25a29++;
      if($y >= 30 && $y <= 34) $e30a34++;
      if($y >= 35 && $y <= 39) $e35a39++;
      if($y >= 40 && $y <= 44) $e40a44++;
      if($y >= 45 && $y <= 49) $e45a49++;
      if($y >= 50 && $y <= 54) $e50a54++;
      if($y >= 55) $e55mas++;
    } else {
      $sinEdad++;
    }

    
  }

  $c["varones"] = $varones;
  $c["menos14"] = $menos14 ?: "";
  $c["14"] = $e14 ?: "";
  $c["15"] = $e15 ?: "";
  $c["16"] = $e16 ?: "";
  $c["17"] = $e17 ?: "";
  $c["18"] = $e18 ?: "";
  $c["19"] = $e19 ?: "";
  $c["20"] = $e20 ?: "";
  $c["21"] = $e21 ?: "";
  $c["22"] = $e22 ?: "";
  $c["23"] = $e23 ?: "";
  $c["24"] = $e24 ?: "";
  $c["25a29"] = $e25a29 ?: "";
  $c["30a34"] = $e30a34 ?: "";
  $c["35a39"] = $e35a39 ?: "";
  $c["40a44"] = $e40a44 ?: "";
  $c["45a49"] = $e45a49 ?: "";
  $c["50a54"] = $e50a54 ?: "";
  $c["55mas"] = $e55mas ?: "";
  $c["sin_edad"] = $sinEdad ?: "";

  $totalPorOrientacion[$c["orientacion"]] += $c["total"];
  $totalAlumnos +=  $c["total"];
  $totalVarones +=  $c["varones"];
   
  array_push($comisiones, $c);
  
 // echo "<pre>";
 // print_r($ca);

  //print_r($ca[0]); 

//   $informe = EntitySqlo::getInstanceRequire("nomina2")->values($ca[0]);
//   array_push($comisiones, $informe); 
// echo "<pre>";
//   print_r($informe); 
  // foreach($ca as $nomina2){
  //   $v = EntitySqlo::getInstanceRequire("nomina2")->values($nomina2);
  // }  
}

//$alumnos = $sqlo->valuesAll($alumnos);

require_once("informeAlumnos/template.html");
