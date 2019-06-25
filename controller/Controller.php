<?php

 class Controller {
    
    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }
    

    public function vistaHome($datos){
        $view = new Home();
        $listaresidencia=PDOResidencia::getInstance()->listarTodas();
        if(empty($datos['user'])){
           $view->show(array('user' => null,'listaresidencia'=> $listaresidencia));
           return true;
        }
         $viewAdmin= new AdminPanel();
     if(!empty($_SESSION['usuario'])){
        if ($_SESSION['tipo'] == "administrador") {
            $viewAdmin->show(array('user' => $_SESSION['usuario'],'listaresidencia'=> $listaresidencia));
            return true;
        }else{
            $this->buscar_semanas();
            return true;
        }
     }
        
        return false;
    }

    public function adminPanel($datos){
        $view= new AdminPanel();
        $view->show(array('user' => $_SESSION['usuario'] ,'listaresidencia'=> $datos['residencias'],'mensaje'=> $datos['mensaje']));
        return true;
    }

    public function userPanel($datos){
      $viewUser= new UserPanel();
      $viewUser->show(array('user' => $_SESSION['usuario'],'listaresidencia'=> $datos['residencias']));
            return true;


    }

    public function vistaExito($mensaje){
        $view = new Exito();
        $view->show($mensaje);

    }

    public function vistaIniciarSesion($datos){
        //recordar refactorizarlo

        $view = new IniciarSesion();
        $view2= new Home();
         if(empty($datos))
           $view->show(array('mensaje' => null));
        else
           $view2->show($datos);  
    }


    public function alta_sesion($usuario,$id, $tipousuario){
        if(!isset($_SESSION)){
            session_start();
         }else{
             session_destroy();
             session_start(); 
         }
        $_SESSION['id'] = $id;
        $_SESSION['usuario']= $usuario;
        $_SESSION['tipo']= $tipousuario;

    }

    public function cerrarSesion(){
        session_destroy();
        $this->vistaExito(array('mensaje' => "Sesión Terminada." ));
    }

    public function verificarDatos(){}

     public function buscar_semanas(){
     $subastas=PDOSubasta::getInstance()->listarTodasSubasta();
     $directas=PDODirecta::getInstance()->listarTodasDirectas();
     $hotsale= PDOHotsale::getInstance()->listarTodosHotsale();
     $view= new Semana();
    if(($subastas != false) || ($directas != false) || ($hotsale != false)){ 
    $view->buscarSemana(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => null,'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
  }
    else{
      $view->buscarSemana(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => "No hay Resultados",'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
    }


   }




}
    