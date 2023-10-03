<?php

class Conexion {

    //Establecimiento de la conexion con la BD
    public static function conectar() {

        $link = new PDO('mysql:host=localhost;dbname=API-REST','root','9774676Dl');
        $link -> exec("set names utf8");
       

        return $link;
    }
}

?>