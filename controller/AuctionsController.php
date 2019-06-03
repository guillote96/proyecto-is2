<?php

require_once('controller/ResidenciaSemanaController.php');

class AuctionsController extends ResidenciaSemanaController {

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
    $auctions = PDOSubasta::getInstance()->getDetailedAuctions(1);
    $auctionsView->show(array("auctions" => $auctions, "user" => $_SESSION['usuario']));
  }

  public function listadoSubastasHabilitadas($idResidencia){

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

   public function crearSubasta($idResidencia){
    $view= new CrearSubasta();
    $semanas= PDOSemana::getInstance()->semanasNoIncluidas($idResidencia);
    $view->show(array('user' => $_SESSION['usuario'], 'semanas' => $semanas,'idResidencia'=> $idResidencia));

   }

   public function procesar_subasta($idResidencia, $idSemana, $base){
    if(empty($idSemana)){
      $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario']));
     return false;

    }

    if(!PDOResidenciaSemana::getInstance()->existeSemanaParaResidencia($idResidencia,$idSemana)){
      PDOResidenciaSemana::getInstance()->insertarSemanaResidencia($idResidencia,$idSemana);
      $idResidenciaSemana=PDOResidenciaSemana::getInstance()->traerIdResidenciaSemana($idResidencia,$idSemana);
      PDOSubasta::getInstance()->insertarSubasta($idResidenciaSemana,$base);
      $this->vistaExito(array('mensaje' => "se registro subasta ;)","user"=> $_SESSION['usuario']));
      return true;
     }

     $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario']));
     return false;
   }

public function finalizarSubasta($idSubasta){

   PDOSubasta::getInstance()->desactivarSubasta($idSubasta);
   $auctionsView = new AuctionsView();
   $auctions = PDOSubasta::getInstance()->getDetailedAuctions(1);
   $auctionsView->show(array("auctions" => $auctions, "user" => $_SESSION['usuario']));


}


 public function sincronizador($idResidencia){
   $datos= PDOSubasta::getInstance()->listarSubasta($idResidencia);
    
    foreach ($datos as $key => $dato){
        
         if (!$dato[1]->getActiva())
           $this->procesarInactivas($dato);
         else
           $this->procesarActivas($dato);
     }


   }


  public function procesarInactivas($dato){

        
        if($dato[1]->getBase() != null){
          //Si tiene base, significa que fue configurada la base por el admin por lo tanto puede activarse.
          $hoy = date_create('2019-12-3');//cambiar por fecha de hoy (la ficticia es para prueba de activacion)
          $hoy= date_format($hoy, 'Y-m-d');

   
        //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de activacion exacta de la subasta
        
         $fecha_inicio = date_create($dato[0]->getFechaInicio());
         date_sub($fecha_inicio, date_interval_create_from_date_string('6 months'));
         $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');

         //Con la fecha de inicio  calculada en el paso anterior, le sumo 3 dias (se cuenta el dia "0" como subasta) para obtener la fecha de cierre

         $fecha_fin = date_create($fecha_inicio);
         date_add($fecha_fin, date_interval_create_from_date_string('3 days'));
         $fecha_fin= date_format($fecha_fin, 'Y-m-d');


          //mientras este en el rango de los primeros 3 dias de la subasta, la misma se activara
         if( ($hoy >= $fecha_inicio) && ($hoy < $fecha_fin)){
          //Activar semana subasta
           PDOSubasta::getInstance()->activarSemanaSubasta($dato[0]->getIdResidenciaSemana());
         }

        }


   }

  public function procesarActivas($dato){
       $hoy = date_create('2019-12-6');//cambiar por fecha de hoy (la ficticia es para prueba de activacion)
       $hoy= date_format($hoy, 'Y-m-d');

   
       //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de activacion exacta de la subasta
        
       $fecha_inicio = date_create($dato[0]->getFechaInicio());
       date_sub($fecha_inicio, date_interval_create_from_date_string('6 months'));
       $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');

       //Con la fecha de inicio  calculada en el paso anterior, le sumo 3 dias (se cuenta el dia "0" como subasta) para obtener la fecha de cierre

       $fecha_fin = date_create($fecha_inicio);
       date_add($fecha_fin, date_interval_create_from_date_string('3 days'));
       $fecha_fin= date_format($fecha_fin, 'Y-m-d');

       //Verifico si ya corresponde tomar un decision (pasar a hotsale o  adjudicar)
       if($hoy > $fecha_fin){
        PDOSubasta::getInstance()->desactivarSemanaSubasta($dato[0]->getIdResidenciaSemana());
        PDOSubasta::getInstance()->borrarSemanaSubasta($dato[0]->getIdResidenciaSemana());
           

           //si tiene pujantes no puede ponerse en hotsale.
           if(PDOSubasta::getInstance()->tieneParticipantes($dato[1]->getIdSubasta())){
                 // ADJUDICAR

            $this->adjudicarSubasta($dato[1]->getIdSubasta());
            /*$idUsuario=PDOSubasta::getInstance()->idUsuarioConMayorPujaEnSubasta($dato[1]->getIdSubasta());
            PDOSubasta::getInstance()->adjudicarSubasta($dato[1]->getIdSubasta(),$idUsuario);*/
            //si no tiene pujantes
           }else{

            PDOHotsale::getInstance()->insertarHotsale($dato[0]->getIdResidenciaSemana());
           //pasar a HOTSALE DESAHABILITADA
          }
       }
}

   public function adjudicarSubasta($idSubasta){
      $idUsuario=PDOSubasta::getInstance()->idUsuarioConMayorPujaEnSubasta($idSubasta);
      $usuario=PDOUsuario::getInstance()->traerUsuario($idUsuario);
      if ($usuario->getCreditos() > 0) {
         PDOUsuario::getInstance()->decrementarCreditos($idUsuario);
         PDOSubasta::getInstance()->adjudicarSubasta($idSubasta,$idUsuario);
      }

  }

}