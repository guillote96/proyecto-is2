<?php
require_once('controller/Controller.php');

class TarifasController extends Controller {

	private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }


   public function traerTarifas($mensaje){
    	 
    	$tarifapremium= PDOTarifa::getInstance()->traerTarifaPremium();
        $tarifaestandar= PDOTarifa::getInstance()->traerTarifaEstandar();


        $view = new Tarifas();
        $view->show(array('mensaje'=>$mensaje,'tarifaestandar' => $tarifaestandar,'tarifapremium' => $tarifapremium));

     }


     public function procesarEdicionTarifas(){

        if(($_POST['tarifaPremium'] && $_POST['tarifaEstandar'])>0){
         
             PDOTarifa::getInstance()->actualizarTarifaPremium($_POST['tarifaPremium']);
             PDOTarifa::getInstance()->actualizarTarifaEstandar($_POST['tarifaEstandar']);
        

             $this->vistaExito(array('mensaje' =>"¡¡¡Las tarifas fueron actualizadas exitosamente!!!",'user' =>$_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));    
         }
         else
            { 

                $mensaje = "Debe ingresar montos superiores a $0 ";
                $this ->traerTarifas($mensaje);
            }


     }
     


}
    