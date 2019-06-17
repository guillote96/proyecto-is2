<?php

require_once('controller/ResidenciaSemanaController.php');

class HotsaleController extends ResidenciaSemanaController {

  private static $instance;

  public static function getInstance() {

    if (!isset(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {

  }


  public function listarPosiblesHotsale(){
     $posiblesHotsale=PDOHotsale::getInstance()->listarTodosHotsaleDeshabilitado();
     if(sizeof($posiblesHotsale) == 0){
        $this->vistaExito(array('mensaje' =>"No hay Posibles hotsale para mostrar! ", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return false;

     }
     $view= new EstadoHotsale();
     $view->show(array('hotsales' => $posiblesHotsale, 'user'=> $_SESSION['usuario']));
     return true;


  }

  public function habilitarHotsale($idResidenciaSemana){
    //Llamar a vista de ingreso de precio.
    $view= new IngresoMonto();
    $view->show(array('idRS'=> $idResidenciaSemana, 'user'=> $_SESSION['usuario']));

  }

  public function procesarHotsale($idResidenciaSemana,$precio){
    PDOHotsale::getInstance()->habilitarHotsale($idResidenciaSemana,$precio);
    $this->vistaExito(array('mensaje' =>"Hotsale Habilitado!! ", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));



  }
  public function comprarSemana($idResidenciaSemana,$idUser){
     PDOHotsale::getInstance()->adjudicarHotsale($idResidenciaSemana,$idUser);
     $this->vistaExito(array('mensaje' =>"Compra Concretada ¡Muchas Gracias!", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
         return true;

  }


}