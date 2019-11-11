<?php
require_once("../../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("class/controller/Transaction.php");

require_once("function/array_unique_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_group_value.php");

echo "* consultar cargas horarias<br>";
$cargas_horarias = cargas_horarias();
echo "+ se han obtenido " . count($cargas_horarias) . " cargas horarias existentes<br>";


foreach($cargas_horarias as $ch){
    echo $ch["id"] . " " . $ch["asi_nombre"] . " " . $ch["horas_catedra"] . "<br>";
}

echo "* definir sql para insertar distribucion horaria<br>";
echo "<pre>";
$insert = insertar_distribucion_horaria(1, 3, "31");
echo $insert["sql"];

$insert = insertar_distribucion_horaria(1, 2, "33");
echo $insert["sql"];

$insert = insertar_distribucion_horaria(1, 2, "34");
echo $insert["sql"];

$insert = insertar_distribucion_horaria(2, 3, "32");
echo $insert["sql"];

$insert = insertar_distribucion_horaria(2, 3, "35");
echo $insert["sql"];










function cargas_horarias(){
  /**
   * cargas horarias existentes
   * seran utilizadas para reducir la cantidad de comisiones a procesar
   */   
  
  $render = new Render();
  $render->setCondition([
      ["anio","=","1"],
      ["semestre","=","1"],
      ["pla_resolucion","=~", "6321"],
      ["pla_orientacion","=~", "gestion"],
      ["pla_resolucion","!=~", "anual"]
  ]);
  return Dba::fetchAll(CargaHorariaSqlo::getInstance()->all($render));
}


function insertar_distribucion_horaria($dia, $horas_catedra, $carga_horaria){
    return DistribucionHorariaSqlo::getInstance()->insert( [
        "horas_catedra" => $horas_catedra,
        "dia" => $dia,
        "carga_horaria" => $carga_horaria
    ]);
}    

