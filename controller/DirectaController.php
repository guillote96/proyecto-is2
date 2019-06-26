<?php

require_once('controller/ResidenciaSemanaController.php');

class DirectaController extends ResidenciaSemanaController {

  private static $instance;

  public static function getInstance() {

    if (!isset(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {

  }

//***************************Desde aca hasta...*******
   public function procesarInactivas($dato){
   	//mientras este en el rango de los primeros 6 meses correspondiente a una semana directa, la misma se activara..
          $hoy = date_create('2019-05-31');//cambiar por fecha de hoy
          $hoy= date_format($hoy, 'Y-m-d');

	 
  	  	//Con la fecha de inicio de la semana, calculo le resto 12 meses para obtener la fecha de activacion
  	  	
  	  	 $fecha_inicio = date_create($dato[0]->getFechaInicio());
         date_sub($fecha_inicio, date_interval_create_from_date_string('1 year'));
         $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');
         //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de cerrado
         $fecha_fin = date_create($dato[0]->getFechaInicio());
         date_sub($fecha_fin, date_interval_create_from_date_string('6 months'));
         $fecha_fin= date_format($fecha_fin, 'Y-m-d');

         if( ($hoy >= $fecha_inicio) && ($hoy < $fecha_fin)){
         	//Activar semana directa
           PDODirecta::getInstance()->activarSemanaDirecta($dato[0]->getIdResidenciaSemana());

         }



   }

    public function procesarActivas($dato){
    	if($dato[1]->getIdPremiumCompra() != null){
    		//No hacer pasaje a subasta por que alguien la compro.Setear el booleano de borrado y desactivar semana.
    	 PDODirecta::getInstance()->borrarSemanaDirecta($dato[0]->getIdResidenciaSemana());
    	 PDODirecta::getInstance()->desactivarSemanaDirecta($dato[0]->getIdResidenciaSemana());

    	 }else{
    	 	// Nadie la compro. Hay que verificar si esta lista para pasarse a subasta
    	    $hoy = date_create('2019-12-02');//cambiar por fecha de hoy
            $hoy= date_format($hoy, 'Y-m-d');

            $fecha_fin = date_create($dato[0]->getFechaInicio());
            date_sub($fecha_fin, date_interval_create_from_date_string('6 months'));
            $fecha_fin= date_format($fecha_fin, 'Y-m-d');

            if($hoy > $fecha_fin){
         	//Se  hace borrado de semana directa y Se inserta tupla en Subasta.
              PDODirecta::getInstance()->borrarSemanaDirecta($dato[0]->getIdResidenciaSemana());
              PDODirecta::getInstance()->desactivarSemanaDirecta($dato[0]->getIdResidenciaSemana());
             PDOSubasta::getInstance()->insertarSubasta($dato[0]->getIdResidenciaSemana(),null);
            }
         }
    }


  public function sincronizador($idResidencia){
  	  $datos= PDODirecta::getInstance()->listarDirectas ($idResidencia);
  	  
  	  
  	   foreach ($datos as $key => $dato){
  	  	
       	 if (!$dato[1]->getActiva())
  	  	   $this->procesarInactivas($dato);
  	     else
           $this->procesarActivas($dato);
        }


     }

//************************HASTA ACA no se USA mas*************************
     public function listarDirectasTodas(){

      //$directas= PDODirecta::getInstance()->listarTodasDirectas();

      //devuelve las que estan lista para activarse
      $directasParaActivar = $this->procesarInactivasV2();
      $directas= $this->procesarActivasV2();

      //devuleve las que estan lista para pasar a subasta




      $view= new EstadoDirecta();
      if(sizeof($directasParaActivar)>0 || sizeof($directas['parasubasta'])>0 || sizeof($directas['directas'])>0){
          
          $view->panelDirectas(array('directasParaActivar' => $directasParaActivar , 'directas'=> $directas , 'idUser' => $_SESSION["id"],'tipousuario' => $_SESSION['tipo'], "user"=> $_SESSION['usuario']));
          return true;

      }else{
         $this->vistaExito(array('mensaje' =>"No Hay semanas directas para Mostrar.", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return false;


      }





     }

     public function comprarSemana($idResidenciaSemana,$idUser){

       $usuario=PDOUsuario::getInstance()->traerUsuario($idUser);
       if ($usuario->getCreditos() > 0) {
         PDOUsuario::getInstance()->decrementarCreditos($idUser);
         PDODirecta::getInstance()->adjudicarDirecta($idResidenciaSemana,$idUser);
         $this->vistaExito(array('mensaje' =>"Compra Concretada ¡Muchas Gracias!", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
         return true;
       }
        $this->vistaExito(array('mensaje' =>"Usted no tiene creditos Suficientes!", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return false;


     }




  public function crearDirecta($idResidencia){
    $view= new CrearDirecta();
    $semanas= PDOSemana::getInstance()->semanasNoIncluidas($idResidencia);
    $view->show(array('user' => $_SESSION['usuario'], 'semanas' => $semanas,'idResidencia'=> $idResidencia));

   }

   public function procesar_directa($idResidencia, $idSemana, $precio){
    if(empty($idSemana)){
      $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
     return false;

    }



    if(!PDOResidenciaSemana::getInstance()->existeSemanaParaResidencia($idResidencia,$idSemana)){
      PDOResidenciaSemana::getInstance()->insertarSemanaResidencia($idResidencia,$idSemana);
     }

     $idResidenciaSemana=PDOResidenciaSemana::getInstance()->traerIdResidenciaSemana($idResidencia,$idSemana);

     if(!PDODirecta::getInstance()->existeResidenciaSemanaDirecta($idResidenciaSemana)){
       PDODirecta::getInstance()->insertarDirecta($idResidenciaSemana,$precio);
       $this->vistaExito(array('mensaje' => "se registro Directa ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
       return true;
     }

     $this->vistaExito(array('mensaje' => "Ups! Hubo un error ;)","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
     return false;
   }


public function activarDirecta($idResidenciaSemana){
    PDODirecta::getInstance()->activarSemanaDirecta($idResidenciaSemana);
    $this->listarDirectasTodas();
}


//Utilizado para dar de alta el boton "habilitar Directa"
  public function procesarInactivasV2(){
    //mientras este en el rango de los primeros 6 meses correspondiente a una semana directa, la misma se activara..
          $hoy = date_create('2019-05-31');//cambiar por fecha de hoy
          $hoy= date_format($hoy, 'Y-m-d');
          $directasParaActivar=array();
          $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){

      $datos= PDODirecta::getInstance()->listarDirectas ($residencia->getIdResidencia());
          

       foreach ($datos as $key => $dato){
        
          if (!$dato["directa"]->getActiva()){
   
         //Con la fecha de inicio de la semana, calculo le resto 12 meses para obtener la fecha de activacion
        
         $fecha_inicio = date_create($dato["residenciasemana"]->getFechaInicio());
         date_sub($fecha_inicio, date_interval_create_from_date_string('1 year'));
         $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');
         //Con la fecha de inicio de la semana, calculo le resto 6 meses para obtener la fecha de cerrado
         $fecha_fin = date_create($dato["residenciasemana"]->getFechaInicio());
         date_sub($fecha_fin, date_interval_create_from_date_string('6 months'));
         $fecha_fin= date_format($fecha_fin, 'Y-m-d');

         if( ($hoy >= $fecha_inicio) && ($hoy < $fecha_fin)){
          //Agregar a arreglo
          $directasParaActivar[]=$dato;

         }

         }
       }

       }

       return $directasParaActivar;



   }

   public function cerrarDirecta($idResidenciaSemana){
    if(!PDODirecta::getInstance()->tieneComprador($idResidenciaSemana)){

       PDODirecta::getInstance()->borrarSemanaDirecta($idResidenciaSemana);
       PDODirecta::getInstance()->desactivarSemanaDirecta($idResidenciaSemana);
       PDOSubasta::getInstance()->insertarSubasta($idResidenciaSemana,null);
       $this->listarDirectasTodas();
       return true;
    }   
    $this->vistaExito(array('mensaje' => "No puede pasarse a subasta. ¡Ya tiene comprador!","user"=> $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
       
       return false;



     }




 public function procesarActivasV2(){
   // Nadie la compro. Hay que verificar si esta lista para pasarse a subasta
     $hoy = date_create('2019-12-02');//cambiar por fecha de hoy
     $hoy= date_format($hoy, 'Y-m-d');

     $directasParaSubasta=array();
     $directasActivas=array();
     $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){

       $datos= PDODirecta::getInstance()->listarDirectas ($residencia->getIdResidencia());
          

       foreach ($datos as $key => $dato){
        
          if ($dato["directa"]->getActiva()){

           $fecha_fin = date_create($dato["residenciasemana"]->getFechaInicio());
           date_sub($fecha_fin, date_interval_create_from_date_string('6 months'));
           $fecha_fin= date_format($fecha_fin, 'Y-m-d');

           if($hoy > $fecha_fin){
            //se agrega en Arreglo de "Pasar a subasta"
            $directasParaSubasta[]=$dato;

           }else{
              // todavia no estan para pasar
             $directasActivas[]=$dato;

           }
    

           }
        }
      }

      return array('parasubasta' =>$directasParaSubasta,'directas'=>$directasActivas);

  }
    

 }



