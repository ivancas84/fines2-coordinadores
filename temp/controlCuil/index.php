<?php
require_once("../../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("function/dni_to_cuil.php");
require_once("function/array_unique_key.php");


$dependencia_ = isset($_GET["dependencia"]) ? $_GET["dependencia"] : "Todos";
$fechaAnio = isset($_GET["fecha_anio"]) ? $_GET["fecha_anio"] : date("Y");
if(isset($_GET["fecha_semestre"])) $fechaSemestre = $_GET["fecha_semestre"];
else  $fechaSemestre = (date("m") < 7) ? 1 : 2;
$clasificacion = isset($_GET["clasificacion"]) ? $_GET["clasificacion"] : "Fines";
$dependencia = ($dependencia_ == "Todos") ?  $_SESSION["dependencia"] : $dependencia_;

$idProfesores = id_profesores($fechaAnio, $fechaSemestre, $clasificacion, $dependencia);
$profesores = Dba::all("id_persona",[["id","=",$idProfesores]]);
control_cuil($profesores);


function id_profesores($fechaAnio, $fechaSemestre, $clasificacion, $dependencia){
  $render = new Render();
  $render->setCondition([
    ["cur_com_dvi_sed_dependencia", "=", $dependencia],
    ["cur_com_dvi__clasificacion_nombre", "=", $clasificacion],
    ["cur_com_fecha_anio", "=", $fechaAnio],
    ["cur_com_fecha_semestre", "=", $fechaSemestre],
    ["cur_com_autorizada", "=", true],
    ["profesor", "=", true]
  ]);
  $profesores = Dba::all("toma",$render);
  return array_unique_key($profesores, "profesor");

}

function control_cuil($profesores){
  foreach($profesores as $persona) {
    if($persona->_controlCuil()){

    }
  }

    $sql = "";  
$i = 0;

foreach($personas as $persona) {
  $i++;
 
  if(empty($persona["numero_documento"] 
  || empty($persona["genero"]) 
  || (strlen($persona["numero_documento"]) < 7) 
  || (strlen($persona["numero_documento"]) > 11)
  || ((strpos(strtolower($persona["genero"]), 'f') !== false) && (strpos(strtolower($persona["genero"]), 'm') !== false)))) {
    echo "ERROR DE DATOS, NO SERA PROCESADO<br>";
    continue;
  }

      
  $g = (strpos(strtolower($persona["genero"]), 'f') !== false) ? "2" : "1";
  $cuil = dni_to_cuil($persona["numero_documento"], $g);

  if(empty($persona["cuil"])) {  
    echo $i . " " ;
    print_persona($persona);
    echo "CUIL VACIO SE CARGARA EL CUIL: " . $cuil . "<br><br>";
    EntitySqlo::getInstanceRequire("id_persona") = new EntitySqlo::getInstanceRequire("id_persona")();
    $row = array("id" => $persona["id"], "cuil" => $cuil);
    $persist = $EntitySqlo::getInstanceRequire("id_persona")->update($row);
    $sql .= $persist["sql"];

  } else {
    if($persona["cuil"] == $cuil) { /*echo "CUIL CORRECTO: " . $cuil . "<br>"; */ }
    else {
     echo $i . " " ;
     print_persona($persona);
     echo "CUIL INCORRECTO VERIFICAR: " . $cuil . "<br><br>";
    }
  }
  


}

echo $sql;
  
  

 
}
 