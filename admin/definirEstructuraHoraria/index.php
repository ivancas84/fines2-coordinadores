<?php
require_once("../../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("class/model/Transaction.php");

require_once("function/array_unique_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_group_value.php");

echo "* consultar cargas horarias existentes<br>";
$cargas_horarias_existentes = cargas_horarias_existentes();
echo "+ se han obtenido " . count($cargas_horarias_existentes) . " cargas horarias existentes<br>";

echo "* consultar id comisiones<br>";
$id_comisiones = id_comisiones($cargas_horarias_existentes);
echo "+ se han obtenido " . count($id_comisiones) . "<br>";

echo "* consultar comisiones horarios<br>";
$comisiones_horarios = comisiones_horarios($id_comisiones);
echo "+ se han obtenido " . count($comisiones_horarios) . "<br>";

echo "* consultar comisiones no validas<br>";
$id_comisiones_no_validas = id_comisiones_no_validas($comisiones_horarios);
echo "+ se han obtenido " . count($id_comisiones_no_validas) . "<br>";

echo "* definir datos a procesar<br>";
foreach($id_comisiones_no_validas as $id) unset($comisiones_horarios[$id]);
$comisiones_cursos_horarios = comisiones_cursos_horarios($comisiones_horarios);
$comision_dias = comision_dias($comisiones_cursos_horarios);
echo "+ se van a procesar " . count($comisiones_cursos_horarios) . " comisiones<br>";

echo "* insertar distribuciones horaria<br>";
$sql = insertar_distribucion_horaria($comisiones_cursos_horarios, $comision_dias);

echo "+ sql ejecutado:<br>";
echo "<pre>";
echo $sql;
echo "</pre>";


function cargas_horarias_existentes(){
  /**
   * cargas horarias existentes
   * seran utilizadas para reducir la cantidad de comisiones a procesar
   */   
  $sqlo = DistribucionHorariaSqlo::getInstance();
  return array_unique_key(Dba::fetchAll($sqlo->all()), "carga_horaria");
}

function id_comisiones($cargasHorariasExistentes = null){
    /**
     * id de las comisiones a procesar
     * no se incluyen aquellas que tengan definida la carga horaria
     * se selecciona la de mayor id (una por cada carga horaria existente)
     */
    $sqlo = CursoSqlo::getInstance();
    $render = new RenderAux;
    $render->setAggregate(["max_comision"]);
    $render->setGroup(["carga_horaria"]);
    $render->setCondition([
        ["horario","=",true],
        ["horario","!=~","00:00"],
    ]);
    if(!empty($cargasHorariasExistentes)) $render->addAdvanced(["carga_horaria", "!=", $cargasHorariasExistentes]);
    $render->setOrder(["max_comision" => "asc"]);
    $sql = $sqlo->advanced($render);
    return array_unique_key(Dba::fetchAll($sql), "max_comision");
}

function comisiones_horarios($comisionesId){
    /**
     * horarios agrupados por comision
     */
    $render = new Render();
    $render->setCondition(["cur_comision","=",$comisionesId]);
    $render->setOrder(["cur_comision" => "ASC", "dia_numero" => "ASC", "hora_inicio" => "ASC"]);
    $sqlo = HorarioSqlo::getInstance();
    $sql = $sqlo->all($render);
    return array_group_value(Dba::fetchAll($sql), "cur_comision");
}


function id_comisiones_no_validas($comisiones_horarios) {
    /**
     * Definir comisiones de horarios no validas
     * La idea es buscar el plan asociado a la comision y repetir el proceso
     */
    $ids = [];
    foreach($comisiones_horarios as $idComision => $horarios){
        foreach($horarios as $horario){
            $values = HorarioSqlo::getInstance()->values($horario);
            $hc = $values["horario"]->horasCatedra();
            if(intval($hc) != floatval($hc)) array_push($ids, $idComision);            
        }
    }
    return $ids;
}

function comisiones_cursos_horarios($comisiones_horarios){
    /**
     * horarios agrupados por curso
     * curso agrupados por comision
     */
    $comisiones_cursos_horarios = [];
    foreach($comisiones_horarios AS $idComision => $horarios_){
        $horarios = array_group_value($horarios_, "curso");
        $comisiones_cursos_horarios[$idComision] = $horarios;
    }
    return $comisiones_cursos_horarios; 
}

function comision_dias($comisiones){
    /**
     * dias agrupados por comision
     */
    $comision_dias = [];
    foreach($comisiones AS $idComision => $cursos){
        if(!in_array($idComision, $comision_dias)) $comision_dias[$idComision] = [];
        foreach($cursos AS $idCurso => $horarios){
            foreach($horarios as $horario){
                if(!in_array($horario["dia"], $comision_dias[$idComision])) array_push($comision_dias[$idComision], $horario["dia"]);
            }
        }
    }

    foreach($comision_dias AS $key => &$value){
        $value = array_map("suma_uno", array_flip($value));
    }
    return $comision_dias;
}


function suma_uno($param){
    return ($param+1);
}


function eliminar_distribuciones_horarias_existentes($comisiones_cursos_horarios, $cargas_horarias_existentes){
    $cargas_horarias = [];
    foreach($comisiones_cursos_horarios as $id_comision => $cursos_horarios){
        foreach($cursos_horarios as $id_curso => $horarios){
            foreach($horarios as $horario){
                
                array_push($cargas_horarias, $horario["cur_carga_horaria"]);
            }
        }
    }
    
}

function insertar_distribucion_horaria($comisiones_cursos_horarios, $comision_dias){
    $sql = "";
    $detail = [];
    foreach($comisiones_cursos_horarios as $id_comision => $cursos_horarios){
        foreach($cursos_horarios as $id_curso => $horarios){
            foreach($horarios as $horario){                                
                $values = HorarioSqlo::getInstance()->values($horario);                
                $ids = Dba::ids("distribucion_horaria",["carga_horaria","=",$values["carga_horaria"]->id()]);
                if(count($ids)) {

                    $delete = Dba::deleteAll("distribucion_horaria",$ids);
                    $sql .= $delete["sql"];
                    $detail =  array_merge($detail, $delete["detail"]);

                }

                $insert = DistribucionHorariaSqlo::getInstance()->insert([
                    "horas_catedra" => $values["horario"]->horasCatedra(),
                    "dia" => $comision_dias[$id_comision][$values["horario"]->dia()],
                    "carga_horaria" => $values["carga_horaria"]->id(),
                ]);

                $sql .= $insert["sql"];
                $detail =  array_merge($detail, $insert["detail"]);


                //echo $values["horario"]->horasCatedra() . "<br>";
                //echo $values["carga_horaria"]->id(). "<br>";
            }
        }
    }

    if($sql){
        Transaction::begin();
        Transaction::update(["descripcion"=> $sql, "detalle" => implode(",",$detail)]);
        Transaction::commit();
    }

    return $sql;
}






