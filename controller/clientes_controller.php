<?php

class ControllerClientes
{

    public function create($datos)
    {

        // echo "<pre>";
        // echo print_r($datos);
        // echo "</pre>";
        // exit;


        // Validar sintaxís del campo nombre
        if (isset($datos['nombre']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $datos['nombre'])) {

            $json = array(
                "status" => 404,
                "detalle" => "error en el campo del nombre permitido solo letras"
            );
            echo json_encode($json, true);

            return;
        }

        // Validar sintaxís del campo apellido
        if (isset($datos['apellido']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $datos['apellido'])) {

            $json = array(
                "status" => 404,
                "detalle" => "error en el campo del apellido permitido solo letras"
            );
            echo json_encode($json, true);

            return;
        }

        //validar sintaxís del campo correo
        if (isset($datos['email']) && !preg_match(
            '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
            $datos['email']
        )) {

            $json = array(
                "status" => 404,
                "detalle" => "error en el campo del email"
            );
            echo json_encode($json, true);

            return;
        }

        //Validar email si esta repetido

        $clientes = ModeloClientes::index("clientes");

        foreach ($clientes as $key => $value) {
            if ($value["email"] == $datos['email']) {

                $json = array(
                    "status" => 404,
                    "detalle" => "El email ya existe"
                );
                echo json_encode($json, true);

                return;
            }
        }

        //Generar los credenciales del cliente id_cliente y llave_secreta
        $id_cliente = str_replace("$", "c", crypt(
            $datos['nombre'] . $datos['apellido'] . $datos['email'],
            '$2a$07$afartwetsdAD52356FEDGsfhsd$'
        ));

        $llave_secreta = str_replace("$", "a", crypt(
            $datos['email'] . $datos['apellido'] . $datos['nombre'],
            '$2a$07$afartwetsdAD52356FEDGsfhsd$'
        ));


        //Se setea los credeciales en el nuevo cliente
        $datos = array(
            "nombre" => $datos["nombre"],
            "apellido" => $datos["apellido"],
            "email" => $datos["email"],
            "id_cliente" => $id_cliente,
            "llave_secreta" => $llave_secreta,
            "created_at" => date('Y-m-d h:i:s'),
            "updated_at" => date('Y-m-d h:i:s')
        );

       

        //Generación de cliente y creacion de respuesta en formato json
        $create = ModeloClientes::create("clientes", $datos);

        if ($create == "OK") {

            $json = array(
                "status" => 404,
                "detalle" => "Se generaron sus credenciales",
                "id_cliente" => $id_cliente,
                "llave_secreta" => $llave_secreta
            );
            echo json_encode($json, true);

            return;
        }
    }
}
