<?php

class ControllerCursos
{


    public function index($pagina)
    {
        //Validar credenciales del cliente

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $value) {

                if (
                    base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                    base64_encode($value['id_cliente'] . ":" . $value['llave_secreta'])
                ) {

                    if ($pagina != null) {

                        $cantidad = 10;
                        $desde = ($pagina-1) * $cantidad;

                        $cursos = ModeloCursos::index("cursos", "clientes", $cantidad, $desde);
                    } else {
                        $cursos = ModeloCursos::index("cursos", "clientes", null, null);
                    }


                    $json = array(
                        "status" => 200,
                        "total_registros" => count($cursos),
                        "detalle" => $cursos
                    );
                    echo json_encode($json, true);

                    return;
                }
            }
        }
    }

    public function create($datos)
    {
        // Validar los credenciales del cliente

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {

                if (
                    base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                    base64_encode($valueCliente['id_cliente'] .
                        ":" . $valueCliente['llave_secreta'])
                ) {
                    //Validar datos

                    foreach ($datos as $key => $valueDatos) {

                        // echo "<pre>";
                        //     echo print_r($valueDatos);
                        //     echo "</pre>";
                        //     exit;
                        //Revisar el preg_match
                        if (!isset($valueDatos) && !preg_match(
                            '/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáeíóúÁÉÍÓÚ] +$/',
                            $valueDatos
                        )) {

                            $json = array(

                                "status" => 404,
                                "detalle" => "Error en el campo " . $key
                            );
                            echo json_encode($json, true);

                            return;
                        }
                    }

                    //Validar que el titulo o la descripcion no esten repetidos

                    $cursos = ModeloCursos::index("cursos", "clientes", null, null);

                    foreach ($cursos as $key => $value) {

                        if ($value->titulo == $datos['titulo']) {
                            $json = array(

                                "status" => 404,
                                "detalle" => "Este titulo no esta disponible; ya existe."
                            );
                            echo json_encode($json, true);
                            return;
                        }

                        if ($value->descripcion == $datos['descripcion']) {
                            $json = array(

                                "status" => 404,
                                "detalle" => "Esta descripción ya existe."
                            );
                            echo json_encode($json, true);
                            return;
                        }
                    }

                    //Llevar datos al modelo
                    $datos = array(
                        'titulo' => $datos['titulo'],
                        'descripcion' => $datos['descripcion'],
                        'instructor' => $datos['instructor'],
                        'imagen' => $datos['imagen'],
                        'precio' => $datos['precio'],
                        'id_creador' => $valueCliente['id'],
                        'created_at' => date('Y-m-d h:i:s'),
                        'update_at' => date('Y-m-d h:i:s')
                    );

                    // echo "<pre>";
                    // echo print_r($datos);
                    // echo "</pre>";
                    // exit;


                    $create = ModeloCursos::create("cursos", $datos);


                    //Respuesta del modelo
                    if ($create == "OK") {
                        $json = array(

                            "status" => 200,
                            "detalle" => "Registro exitoso, su curso ha sido guardado"
                        );
                        echo json_encode($json, true);
                    }
                }
            }
        }
    }

    public function show($id)
    {
        //Validar credenciales del cliente
        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {

                if (
                    base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                    base64_encode($valueCliente['id_cliente'] .
                        ":" . $valueCliente['llave_secreta'])
                ) {
                    //Mostrar todos los cursos
                    $curso = ModeloCursos::show("cursos", "clientes", $id);

                    if (!empty($curso)) {
                        $json = array(

                            "status" => 200,
                            "detalle" => $curso
                        );
                        echo json_encode($json, true);
                        return;
                    } else {
                        $json = array(

                            "status" => 404,
                            "total_registros" => 0,
                            "detalle" => "No existe registro del curso"
                        );
                        echo json_encode($json, true);
                        return;
                    }
                }
            }
        }
    }

    public function update($id, $datos)
    {
        //Validar credenciales del cliente
        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {

                if (
                    "Basic" . base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                    "Basic" . base64_encode($valueCliente['id_cliente'] .
                        ":" . $valueCliente['llave_secreta'])
                ) {
                    //Validar datos

                    foreach ($datos as $key => $valueDatos) {

                        if (!isset($valueDatos) && !preg_match(
                            '/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáeíóúÁÉÍÓÚ] +$/',
                            $valueDatos
                        )) {

                            $json = array(

                                "status" => 404,
                                "detalle" => "Error en el campo " . $key
                            );
                            echo json_encode($json, true);

                            return;
                        }
                    }

                    //Validar id_creador
                    $curso = ModeloCursos::show("cursos", "clientes", $id);

                    foreach ($curso as $key => $valueCurso) {

                        if ($valueCurso->id_creador == $valueCliente['id']) {

                            //Llevar datos al modelo

                            $datos = array(
                                "id" => $id,
                                'titulo' => $datos['titulo'],
                                'descripcion' => $datos['descripcion'],
                                'instructor' => $datos['instructor'],
                                'imagen' => $datos['imagen'],
                                'precio' => $datos['precio'],
                                'updated_at' => date('Y-m-d h:i:s')
                            );

                            $update = ModeloCursos::update("cursos", $datos);

                            if ($update == "OK") {

                                $json =     array(
                                    "status" => 200,
                                    "detalle" => "Registro exitoso, el curso ha sido actualizado"
                                );
                                echo json_encode($json, true);
                                return;
                            } else {
                                $json = array(
                                    "status" => 404,
                                    "detalle" => "No cuenta con la autorización para actualizar
                                                  este curso"
                                );
                                echo json_encode($json, true);
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    public function delete($id)
    {
        //Validar credenciales del cliente

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $key => $valueCliente) {

                if (
                    base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                    base64_encode($valueCliente['id_cliente'] . ":" . $valueCliente['llave_secreta'])
                ) {
                    //Validar id_creador
                    $curso = ModeloCursos::show("cursos", "clientes", $id);

                    foreach ($curso as $key => $valueCurso) {

                        if ($valueCurso->id_creador == $valueCliente['id']) {

                            //Llevar los datos al modelo

                            $delete = ModeloCursos::delete("cursos", $id);

                            if ($delete == "OK") {

                                $json = array(
                                    "status" => 200,
                                    "detalle" => "Curso borrado correctamente"
                                );
                                echo json_encode($json, true);
                                return;
                            }
                        }
                    }
                }
            }
        }
    }
}
