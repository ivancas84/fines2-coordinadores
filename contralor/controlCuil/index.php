<?php


/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../../config/config.php");
require_once("class/controller/Dba.php");
require_once("class/model/Values.php");
require_once("class/model/Sqlo.php");
require_once("function/dni_to_cuil.php");
require_once("function/array_unique_key.php");


$tomas = Dba::all("toma",[
  ["profesor","=",true],
  ["cur_com_fecha_anio","=","2019"],
  ["cur_com_fecha_semestre","=",2],
  ["cur_com_autorizada","=",true],  
]);

$ids = array_unique_key($tomas, "profesor");

$personas = Dba::getAll("id_persona", $ids);

$sql = "";
foreach($personas as $persona) {
  $idp = EntityValues::getInstanceRequire("id_persona", $persona);

  if($idp->_check() !== true){
    
    echo "<br>ERROR DE DATOS: NO SE PROCESARA<br>";
    print_r($idp->_check());
    print_r($idp);
  }

  $cuil = $idp->_calcularCuil();
  if($idp->_isEmptyValue($idp->cuil())){
    echo "<br>CUIL VACIO: SE DEFINIRA SQL PARA ACTUALIZAR " . $idp->nombres() . " ". $idp->apellidos() . "<br>";

    $idp->setCuil($cuil);
    $sql .= EntitySqlo::getInstanceRequire("id_persona")->update($idp->_toArray())["sql"];
  } elseif($cuil !== $idp->cuil()) {
    echo "<br>CUIL DIFERENTE DEL CALCULADO: SE DEFINIRA SQL PARA ACTUALIZAR " . $idp->nombres() . " ". $idp->apellidos() . " " . $idp->cuil() . "<br>";
    $idp->setCuil($cuil);
    $sql .= EntitySqlo::getInstanceRequire("id_persona")->update($idp->_toArray())["sql"];
  }
}

echo "<pre>".$sql;
  
  











