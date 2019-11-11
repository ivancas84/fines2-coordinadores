<?php

require_once("../../config/config.php");
require_once("class/model/Render.php");
require_once("class/controller/Dba.php");

$render = new Render();
$render->setCondition([
  ["profesor","=",false],
  ["cur_com_fecha_anio","=","2019"],
  ["cur_com_fecha_semestre","=","2"],
  ["cur_com_dvi__clasificacion_nombre","=","Fines"],
  ["cur_com_dvi_sed_dependencia","=","1532435439156616"]
]);

$tomas = Dba::all("toma",$render);
$content = "temp/tomasSinDocente/template.html";
require_once("index/index.html");



