<?php

class AuctionsController extends Controller {

  private static $instance;

  public static function getInstance() {

    if (!isset(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {

  }



  public function listAuctions() {
    $auctionsView = new AuctionsView();
    $auctions = PDOSubasta::getInstance()->getDetailedAuctions();
    $auctionsView->show(array("auctions" => $auctions, "user" => $_SESSION['usuario']));
  }

  public function listadoSubastasHabilitadas($idResidencia){
     date_default_timezone_set('America/Argentina/Buenos_Aires');

     $inactivas= PDOSubasta::getInstance()->traerSubastasInactivas($idResidencia);
     $hoy = date('Y-m-d');
     foreach ($inactivas as $key => $subasta) {
         $fecha = date_create($subasta->getFechaInicio());
         date_sub($fecha, date_interval_create_from_date_string('6 months'));
         $f= date_format($fecha, 'Y-m-d');

         if($hoy == $f){

          PDOSubasta::getInstance()->activarSubasta($subasta->getIdSubasta());
         }

      }

      $activas= PDOSubasta::getInstance()->traerSubastasActivas($idResidencia);
       
       return $activas;

   }


   public function estadoSubasta($datos){

      //idFicticio para esta entrega
      $subastas= PDOResidenciaSemana::getInstance()->traerResidenciaSemanasSubastasDeParticipanteActivas(1);
        
       
       if(sizeof($subastas) > 0){
        $view =  new EstadoSubasta(); 
          $view->show(array('datos' => $subastas, 'user' => $_SESSION['usuario']));
          return true;  
        }else{
          $this->vistaExito(array('mensaje' =>"Usted no tiene subastas que este participando y esten activas... ", 'user' =>$_SESSION['usuario']));


        }


   
   }



}