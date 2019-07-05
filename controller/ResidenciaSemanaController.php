<?php
require_once('controller/Controller.php');

class ResidenciaSemanaController extends Controller {

	private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }


   public function sincronizador($idResidencia){
    	//llamar funciones directas, subasta 
    	DirectaController::getInstance()->sincronizador($idResidencia);
        AuctionsController::getInstance()->sincronizador($idResidencia);

     }


     public function editarSemana($idResidenciaSemana,$idResidencia){
      if(!PDODirecta::getInstance()->tieneComprador ($idResidenciaSemana) && !PDOSubasta::getInstance()->tieneParticipantesV2($idResidenciaSemana) && !PDOHotsale::getInstance()->tieneComprador ($idResidenciaSemana)){
        $semana=PDOSemana::getInstance()->traerSemanaDeResidencia($idResidenciaSemana);
        $view = new Semana();
        $view->editarSemana(array("idResidenciaSemana"=>$idResidenciaSemana,"idResidencia"=>$idResidencia,"fecha_inicio"=>$semana[0]->getFechaInicio()));

      }else{

        $this->vistaExito(array('mensaje' =>"¡Tiene compradores o particiapantes! Edicion Denegada", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
      }

     }

     public function editar_semana($idResidenciaSemana,$idResidencia){

         $fecha_inicio = date_create($_POST['fecha_inicio']);
         $fecha_inicio= date_format($fecha_inicio, 'Y-m-d');


         $fecha_fin = date_create($fecha_inicio);
         date_add($fecha_fin, date_interval_create_from_date_string('6 days'));
         $fecha_fin= date_format($fecha_fin, 'Y-m-d');

         $fecha_hoy=date_create(date('Y-m-d'));
         $fecha_hoy=date_format($fecha_hoy,'Y-m-d');
         if($fecha_inicio > $fecha_hoy){

            $residenciasSemanas=PDOResidenciaSemana::getInstance()->traerResidenciaSemanas($idResidencia);
            //verifico si alguna semana de determinada residencia se superpone con las fechas  ingresadas
            $ok=false;
           foreach ($residenciasSemanas as $key => $residenciasSemana) {

              if(($fecha_inicio >= $residenciasSemana->getFechaInicio()  && $fecha_inicio <= $residenciasSemana->getFechaFin()) || ($fecha_fin >= $residenciasSemana->getFechaInicio()  && $fecha_fin <= $residenciasSemana->getFechaFin())){
                 $ok=true;
                 break;
              }
         
             }


            if(!$ok){
              $fecha_creacion=date('Y-m-d');
        PDOSemana::getInstance()-> insertarSemana($fecha_inicio,$fecha_fin,$fecha_creacion);
              $semana=PDOSemana::getInstance()->buscarSemana($fecha_inicio,$fecha_fin);
              PDOResidenciaSemana::getInstance()->actualizarResidenciaSemana($idResidenciaSemana,$semana[0]->getIdSemana());
               $this->vistaExito(array('mensaje' =>"¡Edicion exitosa!", 'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
               return true;


            }else{
                $semana=PDOSemana::getInstance()->traerSemanaDeResidencia($idResidenciaSemana);
                $view = new Semana();
                $view->editarSemana(array("idResidenciaSemana"=>$idResidenciaSemana,"idResidencia"=>$idResidencia,"fecha_inicio"=>$semana[0]->getFechaInicio(),"mensaje"=>"¡Las fechas se superponen con otra!"));


            }

      }else{
          $semana=PDOSemana::getInstance()->traerSemanaDeResidencia($idResidenciaSemana);
                $view = new Semana();
                $view->editarSemana(array("idResidenciaSemana"=>$idResidenciaSemana,"idResidencia"=>$idResidencia,"fecha_inicio"=>$semana[0]->getFechaInicio(),"mensaje"=>"¡La fecha no debe ser menor a la fecha de HOY!"));



      }

    


     }

}
    