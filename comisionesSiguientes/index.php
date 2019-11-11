<?php

require_once("../config/config.php");
require_once("class/model/Data.php");
require_once("class/model/Values.php");
require_once("class/controller/Transaction.php");

require_once("function/array_unique_key.php");
require_once("function/array_combine_keys.php");
require_once("function/array_group_value.php");

$fecha_anio = $_GET["fecha_anio"];
$fecha_semestre = $_GET["fecha_semestre"];

echo "* consultar comisiones autorizadas periodo actual<br>";
$comisiones_autorizadas = comisiones_autorizadas($fecha_anio, $fecha_semestre);
echo "+ se han obtenido " . count($comisiones_autorizadas) . " comisiones autorizadas<br>";

$sql = "";
$detail = [];
foreach($comisiones_autorizadas as $ca){
    $c = EntitySqlo::getInstanceRequire("comision")->values($ca);    
    echo "* procesar comision " . $c["division"]->numero() . "/" . $c["comision"]->tramo() . "<br>";

    $nuevoId = Dba::uniqId();
    $nuevaComision = definir_nueva_comision($c["comision"], $nuevoId);
    
    if(existe_nueva_comision($nuevaComision)){
        echo "+ La comision siguiente ya existe, se continua con la siguiente<br>";
    } else {
        echo "+ Se agregara nueva comision " .  $c["division"]->numero() . "/" . $nuevaComision->tramo() . "<br>"; 
        $persist = EntitySqlo::getInstanceRequire("comision")->insert($nuevaComision->toArray());
        
        $sql .= $persist["sql"];
        $detail = array_merge($detail, $persist["detail"]);
        $persist = actualizar_comision_anterior($c["comision"], $nuevoId);
        $sql .= $persist["sql"];
        $detail = array_merge($detail, $persist["detail"]);
    }
}
/*
if($sql){
    Transaction::begin();
    Transaction::update(["descripcion"=> $sql, "detalle" => implode(",",$detail)]);
    Transaction::commit();
}*/

echo "<pre>";
echo $sql;


function comisiones_autorizadas($fecha_anio, $fecha_semestre){
    $render = new Render();
    $render->setCondition([
        ["fecha_anio","=",$fecha_anio],
        ["fecha_semestre","=",$fecha_semestre],
        ["tramo","!=","32"],                    
        ["autorizada","=",true],
        ["dvi__clasificacion_nombre","=","Fines"]
    ]);
    $render->setOrder(["anio" => "ASC", "semestre"=>"ASC"]);  
    $sql = EntitySqlo::getInstanceRequire("comision")->all($render);
    return Dba::fetchAll($sql);
}

function existe_nueva_comision($nuevaComision){
    $sql = EntitySqlo::getInstanceRequire("comision")->all([
        ["anio","=",$nuevaComision->anio()],
        ["semestre","=",$nuevaComision->semestre()],
        ["division","=",$nuevaComision->division()],
    ]);
    $row = Dba::fetchAssoc($sql);
    return ($row) ?  true : false;
}

function definir_nueva_comision($comision, $nuevoId){
    $nuevaComision = new ComisionValues;
    $nuevaComision->id = $nuevoId;
    $nuevaComision->anio = $comision->anioSiguiente();
    $nuevaComision->semestre = $comision->semestreSiguiente();
    $nuevaComision->comentario = "En espera de confirmación de Referente";
    $nuevaComision->autorizada = true;
    $nuevaComision->apertura = false;
    $nuevaComision->publicar = true;
    $nuevaComision->fechaAnio = $comision->fechaAnioSiguiente(); 
    $nuevaComision->fechaSemestre = $comision->fechaSemestreSiguiente(); 
    $nuevaComision->division = $comision->division();
    return $nuevaComision;
}

function actualizar_comision_anterior($comision, $nuevoId){
    return EntitySqlo::getInstanceRequire("comision")->update([
        "id" => $comision->id(),
        "comision_siguiente" => $nuevoId,
    ]); 
}