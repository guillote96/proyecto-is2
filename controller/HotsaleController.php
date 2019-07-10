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
     $hotsaleActivos= PDOHotsale::getInstance()->listarTodosHotsale();
     $hotsaleFinalizados= PDOHotsale::getInstance()->listarHotsaleFinalizados();
      $view= new EstadoHotsale();
     if(sizeof($posiblesHotsale) == 0 && sizeof($hotsaleActivos) == 0 && sizeof($hotsaleFinalizados) == 0){
        $view->show(array('mensaje' =>"No hay hotsale para mostrar", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return false;

     }
    
     $view->show(array('hotsales' => $posiblesHotsale,'hotsalesactivos'=>$hotsaleActivos,'hotsalesfinalizados' => $hotsaleFinalizados, 'user'=> $_SESSION['usuario']));
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

    public function sincronizador($idResidencia){
      $datos= PDOHotsale::getInstance()->listarHotsale($idResidencia);
      
       foreach ($datos as $key => $dato){
           $this->procesarActivas($dato);
        }


     }


    public function procesarActivas($dato){
      if($dato["hotsale"]->getIdUsuario() != null){
        //No hacer pasaje a subasta por que alguien la compro.Setear el booleano de borrado y desactivar semana.
        PDOHotsale::getInstance()->borrarSemanaHotsale($dato["residenciasemana"]->getIdResidenciaSemana());
       

       }else{
        // Nadie la compro. Hay que verificar si esta lista para pasarse a subasta
            $hoy = date_create('2020-05-24');//cambiar por fecha de hoy
            $hoy= date_format($hoy, 'Y-m-d');

            if($hoy == $dato["residenciasemana"]->getFechaInicio()){
          //Se  hace borrado de semana directa y Se inserta tupla en Subasta.
              PDOHotsale::getInstance()->borrarSemanaHotsale($dato["residenciasemana"]->getIdResidenciaSemana());
            }
         }
    }


}