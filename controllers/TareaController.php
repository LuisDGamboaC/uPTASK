<?php 

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {
    public static function index() {
        $proyectoId = $_GET['id'];

        if(!$proyectoId) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoId);

        session_start();

        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belognsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            session_start();

            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url', $proyectoId);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al agregar la tarea' // tratando de hacer trampa
                ];
                echo json_encode($respuesta);
                return;
            }
            // Todo bien, instanciar y crear la tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id; // obtenemos el id que consultamos previamente proyectoid arriba
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea Creada Correctamente' ,
                'proyectoId' => $proyecto->id // aparece en el resultado de agregarTarea .json JS
            ];
            echo json_encode($respuesta); // lee el mensaje javascript
            
        }
    }

    public static function actualizar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // capa de seguridad Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            session_start();

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al actualizar la tarea' // tratando de hacer trampa
                ];
                echo json_encode($respuesta);
                return;
            }
            // iNSTANCIAR LA TAREA PERO CON EL NUEVO ESTADO DE 0 A 1
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id; // en soncronia virtual dom y active record

            $resultado = $tarea->guardar();
            if($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id, // aparece en el resultado de agregarTarea .json JS
                    'mensaje' => 'Actualizado Correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }            
        }
    }

    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

           // capa de seguridad Validar que el proyecto exista
           $proyecto = Proyecto::where('url', $_POST['proyectoId']);

           session_start();

           if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
               $respuesta = [
                   'tipo' => 'error',
                   'mensaje' => 'Hubo un Error al actualizar la tarea' // tratando de hacer trampa
               ];
               echo json_encode($respuesta);
               return;
           }           

           $tarea = new Tarea($_POST);
           $resultado = $tarea->eliminar();

           $resultado = [
            'resultado' => $resultado,
            'mensaje' => 'Eliminado correctamente',
            'tipo' => 'exito'
           ];
            echo json_encode($resultado); // {respuesto: 'Correcto'} ['respuesto' => 'correcto] lo que aparece en el console.log(resultado) puede ser cualquier nombre 

        }
    }
}