<?php

/*Por medio de metodos propios de php se lee la url para convertirla en un array y por cada posicion
se va a distinguir de cada peticion, segun el valor contenido como cadena de valores o numerico

*/
// lectura del array de la URL 
$arrayRutas = explode("/", $_SERVER['REQUEST_URI']);

// echo "<pre>";
// echo print_r($arrayRutas);
// echo "</pre>";

//
if (isset($_GET['pagina']) && is_numeric($_GET['pagina'])) {
    $cursos = new ControllerCursos();
    $cursos->index($_GET['pagina']);
} else {

		//Cuando no se hace ninguna petición
		
    if (count(array_filter($arrayRutas)) == 2) {
        $json = array(

            "detalle" => "No encontrado"
        );
        echo json_encode($json, true);
    } else {

        if (count(array_filter($arrayRutas)) == 3) {

            //Se hace peticiones a cursos
            if (array_filter($arrayRutas)[3] == "cursos") {

                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {


                    //Se Capturan datos
                    $datos = array(
                        'titulo' => $_POST['titulo'],
                        'descripcion' => $_POST['descripcion'],
                        'instructor' => $_POST['instructor'],
                        'imagen' => $_POST['imagen'],
                        'precio' => $_POST['precio']
                    );
                    // echo "<pre>";
                    // echo print_r($datos);
                    // echo "</pre>";

                    $cursos = new ControllerCursos();
                    $cursos->create($datos);
                } elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "GET") {

                    $cursos = new ControllerCursos();
                    $cursos->index(null);
                }
            }

             
			//Cuando se hace peticiones desde registro
            if (array_filter($arrayRutas)[3] == "registro") {

                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {

                    $datos = array(
                        "nombre" => $_POST['nombre'],
                        "apellido" => $_POST['apellido'],
                        "email" => $_POST['email']
                    );

                    $clientes = new ControllerClientes();
                    $clientes->create($datos);
                }
            }
            
				//Peticiones GET
        } elseif (array_filter($arrayRutas)[3] == "cursos" && is_numeric(array_filter($arrayRutas)[4])) {

            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "GET") {

                $curso = new ControllerCursos();
                $curso->show(array_filter($arrayRutas)[4]);
            }

            //Peticiones PUT
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "PUT") {

                //capturar los datos
                $datos = array();
                parse_str(file_get_contents('php://input'), $datos);



                $editarCurso = new ControllerCursos();
                $editarCurso->update(array_filter($arrayRutas)[4], $datos);
            }

            //Peticiones DELETE
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "DELETE") {

                $borrarCurso = new ControllerCursos();
                $borrarCurso->delete(array_filter($arrayRutas)[4]);
            }
        }
    }
}
