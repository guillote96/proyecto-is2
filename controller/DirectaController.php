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

   public function procesarInactivas($dato){
   	//mientras este en el rango de los primeros 6 meses correspondiente a una semana directa, la misma se activara..
          $hoy = date_create('2019-11-30');//cambiar por fecha de hoy
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
    	    $hoy = date_create('2019-12-01');//cambiar por fecha de hoy
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

 }



