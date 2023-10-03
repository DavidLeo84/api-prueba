<?php

require_once "controller/rutas_controller.php";
require_once "controller/cursos_controller.php";
require_once "controller/clientes_controller.php";
require_once "model/clientes_modelo.php";
require_once "model/cursos_modelo.php";
require_once "model/conexion.php";


$rutas = new controllerRutas;
$rutas->inicio();
