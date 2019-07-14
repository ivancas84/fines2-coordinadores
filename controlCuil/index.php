<?php


/**
 * Contralor definido en base a los siguientes requerimientos (establecidos por la DEA en marzo de 2019)
 */

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("config/valuesClasses.php");
require_once("function/dni_to_cuil.php");



$fechaAnio = (!empty($_GET["fecha_anio"])) ? $_GET["fecha_anio"] : null;
$fechaSemestre = (!empty($_GET["fecha_semestre"])) ? $_GET["fecha_semestre"] : null;
$title = "Docentes DEA";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];


$fechaAnio = (!empty($_GET["fecha_anio"])) ? $_GET["fecha_anio"] : null;
$fechaSemestre = (!empty($_GET["fecha_semestre"])) ? $_GET["fecha_semestre"] : null;
$title = "Docentes DEA";

$fechaAnioOptions = [2019, 2018, 2017, 2016];
$fechaSemestreOptions = [1, 2];

if(!$fechaAnio || !$fechaSemestre) { //si no esta definido el periodo se da la opcion de definirlo
    $content = "_periodo/index.html";
    $action = "";
    
    require_once("index/menu.html");
    return;
}

$filtros = [
    ["cur_com_dvi_sed_dependencia", "=", $_SESSION["dependencia"]],
    ["cur_com_dvi__clasificacion", "=", $_SESSION["clasificacion"]],
    ["cur_com_fecha_anio", "=", $fechaAnio],
    ["cur_com_fecha_semestre", "=", $fechaSemestre],
    ["cur_com_autorizada", "=", true],
    ["profesor", "=", true]
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["pro_apellidos"=>"asc", "pro_nombres"=>"asc"]);

$sql = TomaSqlo::getInstance()->profesorSumaHorasCatedraAll($render);
$horas = Dba::fetchAll($sql);
$idsProfesores = array_values(array_unique(array_column ($horas ,"profesor")));
$personas = Dba::getAll("id_persona", $idsProfesores);





function print_persona($persona){
  echo $persona["apellidos"] . " " . $persona["nombres"] . " " . $persona["numero_documento"] . " " . $persona["cuil"] . " " . $persona["genero"] . " " . $persona["fecha_nacimiento"] . "<br>";

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
    $idPersonaSqlo = new IdPersonaSqlo();
    $row = array("id" => $persona["id"], "cuil" => $cuil);
    $persist = $idPersonaSqlo->update($row);
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
  
  











