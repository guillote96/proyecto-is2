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
    
    $auctions = PDOSubasta::getInstance()->getDetailedAuctions(1);
    if(sizeof($auctions) == 0){
        $this->vistaExito(array('mensaje' =>"No hay subastas para mostrar! ", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return false;

     }
    $auctionsView = new AuctionsView();
    $auctionsView->show(array("auctions" => $auctions, "user" => $_SESSION['usuario']));
    return true;
  }

  public function listadoSubastasHabilitadas($idResidencia){

      $activas= PDOSubasta::getInstance()->traerSubastasActivas($idResidencia);
       
       return $activas;

   }


   public function estadoSubasta($datos){

      //idFicticio para esta entrega
      $subastas= PDOResidenciaSemana::getInstance()->traerResidenciaSemanasSubastasDeParticipanteActivas($_SESSION['id']);
        
       
       if(sizeof($subastas) > 0){
        $view =  new EstadoSubasta(); 
          $view->show(array('datos' => $subastas, 'user' => $_SESSION['usuario']));
          return true;  
        }else{
          $this->vistaExito(array('mensaje' =>"Usted no tiene subastas que este participando y esten activas... ", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));


        }


   
   }


   public function crearSubasta($idResidencia){
    $view= new CrearSubasta();
    $semanas= PDOSemana::getInstance()->semanasNoIncluidas($idResidencia);
    $view->show(array('user' => $_SESSION['usuario'], 'semanas' => $semanas,'idResidencia'=> $idResidencia));

   }

   public function procesar_subasta($idResidencia, $idSemana, $base){
    if(empty($idSemana)){
      $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
     return false;

    }

    if(!PDOResidenciaSemana::getInstance()->existeSemanaParaResidencia($idResidencia,$idSemana)){
      PDOResidenciaSemana::getInstance()->insertarSemanaResidencia($idResidencia,$idSemana);
      $idResidenciaSemana=PDOResidenciaSemana::getInstance()->traerIdResidenciaSemana($idResidencia,$idSemana);
      PDOSubasta::getInstance()->insertarSubasta($idResidenciaSemana,$base);
      $this->vistaExito(array('mensaje' => "se registro subasta ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
      return true;
     }

     $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
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

            $this->adjudicarSubasta($dato[1]->getIdSubasta(),null);
            //si no tiene pujantes
           }else{

            PDOHotsale::getInstance()->insertarHotsale($dato[0]->getIdResidenciaSemana());
           //pasar a HOTSALE DESAHABILITADA
          }
       }
}

   public function adjudicarSubasta($idSubasta,$idResidenciaSemana){
    if(!PDOSubasta::getInstance()->tieneGanador($idSubasta)){
      PDOSubasta::getInstance()->desactivarSemanaSubasta($idResidenciaSemana);
      PDOSubasta::getInstance()->borrarSemanaSubasta($idResidenciaSemana);
      $idUsuario=PDOSubasta::getInstance()->idUsuarioConMayorPujaEnSubasta($idSubasta);
      $usuario=PDOUsuario::getInstance()->traerUsuario($idUsuario);
      if ($usuario->getCreditos() > 0) {
         PDOUsuario::getInstance()->decrementarCreditos($idUsuario);
         PDOSubasta::getInstance()->adjudicarSubasta($idSubasta,$idUsuario);
         $this->vistaExito(array('mensaje' => "Se adjudico subasta!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
         return true;
      }
      $this->vistaExito(array('mensaje' => "El usuario ganador no tiene creditos. No se adjudico!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
      return false;

     }
     $this->vistaExito(array('mensaje' => "Esta subasta ya esta adjudicada!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
     return false;
  }

  public function listarSubastasSinMontos(){

    $lista=PDOSubasta::getInstance()->listarSubastaInactivasSinMonto();
    $subastas=$this->procesarActivasV2();
    if(sizeof($lista)> 0 || sizeof($subastas['paraadjudicar'])> 0 || sizeof($subastas['parahotsale'])>0 || sizeof($subastas['subastas'])> 0 ){
        $view= new EstadoSubasta();
        $view->listarSubastasInactivasSinMonto(array('datos'=> $lista,'subastas'=>$subastas, 'user'=> $_SESSION['usuario']));
    }else{
      $this->vistaExito(array('mensaje' => "No hay Subastas para completar","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));


    }


  }

  public function cargarMontoSubasta($idResidenciaSemana,$base){
    if(!PDOSubasta::getInstance()->tieneMonto($idResidenciaSemana)){

    PDOSubasta::getInstance()->actualizarBase($idResidenciaSemana,$base);
    $this->procesarInactiva($idResidenciaSemana);
    $this->vistaExito(array('mensaje' => "Se completo subasta..","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
    return true;
    }
    $this->vistaExito(array('mensaje' => "¡Ya existe monto para la subasta!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
    return false;



  }


   public function procesarInactiva($idResidenciaSemana){
          $hoy = date_create('2019-12-3');//cambiar por fecha de hoy (la ficticia es para prueba de activacion)
          $hoy= date_format($hoy, 'Y-m-d');
        $dato=PDOSubasta::getInstance()->traerSubasta($idResidenciaSemana);
        //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de activacion exacta de la subasta
        
         $fecha_inicio = date_create($dato[0][0]->getFechaInicio());
         date_sub($fecha_inicio, date_interval_create_from_date_string('6 months'));
         $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');

         //Con la fecha de inicio  calculada en el paso anterior, le sumo 3 dias (se cuenta el dia "0" como subasta) para obtener la fecha de cierre

         $fecha_fin = date_create($fecha_inicio);
         date_add($fecha_fin, date_interval_create_from_date_string('3 days'));
         $fecha_fin= date_format($fecha_fin, 'Y-m-d');


          //mientras este en el rango de los primeros 3 dias de la subasta, la misma se activara
         if( ($hoy >= $fecha_inicio) && ($hoy < $fecha_fin)){
          //Activar semana subasta
           PDOSubasta::getInstance()->activarSemanaSubasta($dato[0][0]->getIdResidenciaSemana());
         }


   }

   public function procesarActivasV2(){
       $hoy = date_create('2019-12-6');//cambiar por fecha de hoy (la ficticia es para prueba de activacion)
       $hoy= date_format($hoy, 'Y-m-d');
       $paraHotsale=array();
       $paraAdjudicar=array();
       $subastas=array();
       $residencias= PDOResidencia::getInstance()->listarTodas();

       foreach ($residencias as $key => $residencia){

       $datos= PDOSubasta::getInstance()->listarSubasta ($residencia->getIdResidencia());
       
       foreach ($datos as $key => $dato){
        
         if ($dato["subasta"]->getActiva()){

     
       //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de activacion exacta de la subasta
        
       $fecha_inicio = date_create($dato["residenciasemana"]->getFechaInicio());
       date_sub($fecha_inicio, date_interval_create_from_date_string('6 months'));
       $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');

       //Con la fecha de inicio  calculada en el paso anterior, le sumo 3 dias (se cuenta el dia "0" como subasta) para obtener la fecha de cierre

       $fecha_fin = date_create($fecha_inicio);
       date_add($fecha_fin, date_interval_create_from_date_string('3 days'));
       $fecha_fin= date_format($fecha_fin, 'Y-m-d');

       //Verifico si ya corresponde tomar un decision (pasar a hotsale o  adjudicar)
       if($hoy > $fecha_fin){
       /* PDOSubasta::getInstance()->desactivarSemanaSubasta($dato[0]->getIdResidenciaSemana());
        PDOSubasta::getInstance()->borrarSemanaSubasta($dato[0]->getIdResidenciaSemana());*/
           

           //si tiene pujantes no puede ponerse en hotsale.
           if(PDOSubasta::getInstance()->tieneParticipantes($dato["subasta"]->getIdSubasta())){
              $paraAdjudicar[]=$dato;
                
            
           }else{

             $paraHotsale[]=$dato;
          }
       }else{

        $subastas[]=$dato;
       }
       }
       }
     }

     return array('parahotsale' =>$paraHotsale , 'paraadjudicar'=> $paraAdjudicar, 'subastas'=>$subastas);
}

public function pasarAhotsale($idResidenciaSemana){
  if(!PDOSubasta::getInstance()->tieneParticipantesV2($idResidenciaSemana)){
  PDOSubasta::getInstance()->desactivarSemanaSubasta($idResidenciaSemana);
  PDOSubasta::getInstance()->borrarSemanaSubasta($idResidenciaSemana);
  PDOHotsale::getInstance()->insertarHotsale($idResidenciaSemana);
   $this->vistaExito(array('mensaje' => "¡Se paso a posible hotsale!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
  return true;
  }
   $this->vistaExito(array('mensaje' => "Esta subasta ya tiene participantes. Pasaje invalido","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
   return false;

}

}