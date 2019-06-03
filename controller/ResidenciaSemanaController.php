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

}
    