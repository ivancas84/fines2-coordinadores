<?php
require_once("../config/config.php");
require_once("class/model/Dba.php");
require_once("class/model/Sqlo.php");
require_once("class/model/Values.php");
require_once("function/array_combine_key.php");

$idComision = $_GET["id"];
$title = "Horarios Comision";

$filtros = [
    ["cur_comision", "=", $idComision],
];

$render = new Render();
$render->setCondition($filtros);
$render->setOrder(["dia_numero" => "ASC", "hora_inicio" => "ASC"]);

$hsqlo = EntitySqlo::getInstanceRequire("horario");
$sql = $hsqlo->all($render);
$horarios = Dba::fetchAll($sql);
$d = $hsqlo->values($horarios[0]);
//echo "<pre>";
//print_r($d);
$title = "Horarios comisión";
$content = "horariosComision/template.html";
require_once("index/menu.html");


/*
function get_main_data($d){
  return [
    "comision" => new ComisionValues($d["curso_"]["comision_"]),
    "division" => new DivisionValues($d["curso_"]["comision_"]["division_"]),
    "sede" => new SedeValues($d["curso_"]["comision_"]["division_"]["sede_"]),
    "domicilio" => new DomicilioValues($d["curso_"]["comision_"]["division_"]["sede_"]["domicilio_"]),
    "plan" => new PlanValues($d["curso_"]["carga_horaria_"]["plan_"]),

  ];

}

function get_data($d){

  ///echo "<pre>";
  //print_r($d);
  
 

  //"toma" => (isset($d["toma_activa"])) ? new TomaValues($d["toma_activa_"]) : new TomaValues(),
  
  $ret = [
      "horario" => new HorarioValues($d),
      "asignatura" => new AsignaturaValues($d["curso_"]["carga_horaria_"]["asignatura_"]),
      "carga_horaria" => new CargaHorariaValues($d["curso_"]["carga_horaria_"]),      
      
      "dia" => new DiaValues($d["dia_"]),
      //"persona" => new IdPersonaValues($profesores[$d["curso_"][profesor"]]),
      //"suma_horas_catedra" => $d["suma_horas_catedra"],      
  ];

  if(isset($d["curso_"]["toma_activa"])) {    
    $docente = new IdPersonaValues($d["curso_"]["toma_activa_"]["profesor_"]);
    $ret["docente"] = $docente->nombre("AA, Nn");
    $ret["telefonos"] = $docente->telefonos();

    $toma = new TomaValues($d["curso_"]["toma_activa_"]);

    switch($toma->estado()){
      case "Error": case "Renuncia": $ret["background"]  = "table-danger"; break;
      case "Aprobada": $ret["background"] = "table-success"; break;
      case "Pendiente": $ret["background"] = "table-warning"; break;
      default: $ret["background"] = "";
    }
  } else {
    $ret["docente"] = "Sin docente";
    $ret["telefonos"] = "";

  }

  return $ret;

}

*/