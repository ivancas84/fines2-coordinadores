<?php

/**
 * Cargar horarios de cursos de un determinado periodo (fecha anio, fecha semestre)
 */
require_once("../../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("class/model/Transaction.php");

require_once("function/array_unique_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_group_value.php");

$fecha_anio = $_GET["fecha_anio"];
$fecha_semestre = $_GET["fecha_semestre"];

echo "* consultar cursos autorizados del periodo {$fecha_anio}-{$fecha_semestre}: ";
$cursos_autorizados = cursos_autorizados($fecha_anio, $fecha_semestre);
echo count($cursos_autorizados) . " registros<br>";

echo "* definir ids de cargas horarias de cursos: ";
$id_cargas_horarias = array_unique_key($cursos_autorizados, "carga_horaria");
echo count($id_cargas_horarias) . " registros<br>";

echo "* agrupar cursos por comision: ";
$comisiones_cursos = array_group_value($cursos_autorizados, "comision");
echo count($comisiones_cursos) . " registros<br>";

echo "* definir ids de comisiones: ";
$id_comisiones = array_keys($comisiones_cursos);
echo count($id_comisiones) . " registros<br>";

echo "* horarios anteriores: ";
$horarios_anteriores = horarios_anteriores($id_comisiones); //cuidado! puede haber casos que un curso es siguiente de mas de un curso anterior
echo count($horarios_anteriores) . " registros<br>";


echo "* agrupar horarios anteriores por comision: ";
$comisiones_horarios = array_group_value($horarios_anteriores, "cur_com_comision_siguiente");
echo count($comisiones_horarios) . " registros<br>";


echo "* definir dias por comision: ";
$comision_dias = comision_dias($comisiones_horarios);
echo count($comision_dias) . " registros<br>";


echo "* consultar distribuciones horarias: ";
$distribuciones_horarias = distribuciones_horarias($id_cargas_horarias);
echo count($distribuciones_horarias) . " registros<br>";

echo "* agrupar distribuciones horarias por carga horaria: ";
$chs_dhs = array_group_value($distribuciones_horarias, "carga_horaria");
echo count($chs_dhs) . " registros<br>";



foreach($comisiones_cursos as $idc => $cursos){

    if(!tiene_dias($idc, $comision_dias, $cursos[$idc])) continue;
    
    
   
    $dias = $comision_dias[$idc]["dias"];
    $hora_inicio = $comision_dias[$idc]["hora_inicio"];
    $definido = [];
    /** 
     * Array multiple donde se definen los dias y las horas ya procesadas 
     * Ejemplo de elemento: ["dia"=>1, "horas_catedra"=>2]
     */
    foreach($cursos as $curso){
        if(!key_exists($curso["carga_horaria"], $chs_dhs)) {
            echo "*El curso no posee distribuciones horarias asociadas<br>";
            continue;
        }        
        $dhs = $chs_dhs[$curso["carga_horaria"]];
        echo "<pre>";

        foreach($dhs as $dh){
            if(empty($dias[$dh["dia"]-1])) {
                echo "<pre>";
                echo "error al definir día";
                print_r($comision_dias[$idc]);
                echo $dh["dia"]-1;
                print_r($curso);

                echo "</pre>";
                break;
            }


            $hora_inicio_ = clone $hora_inicio;
            foreach($definido as $def){            
                if($def["dia"] == $dh["dia"]) {                    
                    $minutos = $def["horas_catedra"] * 40; 
                    $hora_inicio_->add(new DateInterval('PT' . $minutos . 'M'));                
                }
            }
    
            array_push($definido, ["dia" => $dh["dia"], "horas_catedra" => $dh["horas_catedra"]]);
            $hora_fin = clone $hora_inicio_;
            $minutos = $dh["horas_catedra"] * 40; 
            $hora_fin->add(new DateInterval('PT' . $minutos . 'M'));                

            $horario = new HorarioValues;
            $horario->horaInicio = $hora_inicio_;
            $horario->horaFin = $hora_fin;
            $horario->curso = $curso["id"];
            $horario->dia = $dias[$dh["dia"]-1];
            
            $persist = HorarioSqlo::getInstance()->insert($horario->toArray());
            echo $persist["sql"];
        }
        
    }

}
//print_r($comision_dias);

function cursos_autorizados($fecha_anio, $fecha_semestre){
    $render = new Render();
    $render->setAdvanced([
        ["com_fecha_anio","=",$fecha_anio],
        ["com_fecha_semestre","=",$fecha_semestre],
        ["com_autorizada","=",true],
        ["com_tramo","!=","11"],
        ["com_apertura","=",false],
        ["horario","=",false],
        ["comision","=",true],
        ["com_dvi__clasificacion_nombre","=","Fines"]
    ]);
    $render->setOrder(["comision" => "ASC"]);  
    $sql = CursoSqlo::getInstance()->all($render);
    return Dba::fetchAll($sql);
}

function distribuciones_horarias($id_cargas_horarias){
    $render = new Render();
    $render->setAdvanced(["carga_horaria","=",$id_cargas_horarias]);
    $render->setOrder(["carga_horaria"=>"asc"]);
    $sql = DistribucionHorariaSqlo::getInstance()->all($render);
    return Dba::fetchAll($sql);
}

function horarios_anteriores($id_comisiones){
    $render = new Render();
    $render->setAdvanced([
        ["cur_com_comision_siguiente","=",$id_comisiones],
        ["cur_horario","=",true],
    ]);
    $render->setOrder(["cur_comision" => "ASC", "dia_numero" => "asc", "hora_inicio" => "ASC"]);
    $sql = HorarioSqlo::getInstance()->all($render);

    return Dba::fetchAll($sql);
}

function comision_dias($comisiones_horarios){
    $comision_dias = [];
    foreach($comisiones_horarios as $idc => $horarios){
        $comision_dias[$idc] = [ "hora_inicio"=> null, "dias"=> [] ];
        foreach($horarios as $horario){
            $h = HorarioSqlo::getInstance()->values($horario);
            if(!in_array($h["dia"]->id(), $comision_dias[$idc]["dias"])) array_push($comision_dias[$idc]["dias"], $h["dia"]->id());
            if(!$comision_dias[$idc]["hora_inicio"])  $comision_dias[$idc]["hora_inicio"] = $h["horario"]->horaInicio;
        }
    }
    return $comision_dias;
}

function tiene_dias($idc, $comision_dias, $curso){
    /**
     * Se independiza el metodo para facilitar la impresion de datos y debuggear
     */
    if(key_exists($idc, $comision_dias) === false) {
        echo "<pre>";
        echo "*La comisión no posee días para {$idc}<br>";
        //print_r($curso);
        //print_r($comision_dias);
        echo "</pre>";
        return false;
    }
    return true;
}