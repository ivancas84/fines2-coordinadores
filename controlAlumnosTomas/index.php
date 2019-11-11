<?php

require_once("../config/config.php");
require_once("class/controller/Dba.php");
require_once("class/model/Data.php");

$sql = Data::controlAlumnosTomas();
$rows = Dba::fetchAll($sql);

print_r($rows);