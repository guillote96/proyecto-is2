<?php

require_once('controller/UsuarioController.php');



class PremiumController extends UsuarioController {

	 private static $instance;

     public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }


	public function existePremium ($id){
		// consulta en la tabla de premium de la BD para saber si existe


	}









}