<?php

namespace Controllers;

use EmptyIterator;
use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {

        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belognsTo('propietarioId', $id);


        

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        session_start();
        isAuth();
        $alertas =[];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            
            // Validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // Generar un URL único o token
                $proyecto->url = md5(uniqid());

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }

        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');


        // Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']) { // medida de seguridad
            header('Location: /dashboard');
        }


        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    } 
    
    public static function perfil(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if(empty($alertas)) {
                // Busca si ese correo ya existe en la base de datos lo busca
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // MOstrar mensaje de error
                    Usuario::setAlerta('error', 'Cuenta ya Registrada');
                    $alertas = $usuario->getAlertas(); 
                } else {
                    // Guardar el registro
                    //Guardar el ussuairo
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guradado Correctamente');
                    $alertas = $usuario->getAlertas(); 

                    // Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }

        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,// 'usuario' tiene que ser el mismo que en el value echo $usuario->nombre
            'alertas' => $alertas
        ]);
    } 

    public static function cambiar_password(Router $router) { // BackEND
        session_start();
        isAuth();
        $alertas =[];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']); // Identificamos al usuario que desea cambiar su password

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobar_password();
                
                if($resultado) {
                    $usuario->password = $usuario->password_nuevo;
                    // Eliminar propiedades No necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    // Actualizar 
                    $resultado = $usuario->guardar();
                    
                    if($resultado) {
                    Usuario::setAlerta('exito', 'Password Guardado Correctamente'); // si el password cambio correctamente
                    $alertas = $usuario->getAlertas();
                    }

                }else {
                    Usuario::setAlerta('error', 'Password Incorrecto'); // si el passwrod actual no es el mismo
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password', // Literalmente el titulo que aparece en la pagina
            'alertas' => $alertas
        ]);
    }
}