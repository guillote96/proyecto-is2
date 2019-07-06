<?php

class PDOSolicitud extends PDORepository {

	//IMPORTANTE: La clase esta esta armada con cruce de datos entre residencia y semana

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }



    public function pasarAEstandar($idUsuario){

        $answer = $this->queryList("UPDATE premium SET borrada = 1 WHERE idUsuario=:idUsuario" ,array(':idUsuario'=> $idUsuario));
        
     
   } 
   public function pasarAPremium($idUsuario){

        $answer = $this->queryList("UPDATE premium SET borrada = 0 WHERE idUsuario=:idUsuario" ,array(':idUsuario'=> $idUsuario));
        
     
   }
    public function insertarNuevoPremium($idUsuario){       
        
        $answer = $this->queryList("INSERT INTO premium (idUsuario) VALUES (:idUsuario);", array(':idUsuario' => $idUsuario));
    }

    

    public function yaFuePremium($idUsuario){
      $answer = $this->queryList("SELECT * FROM premium WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

      return (sizeof($answer) > 0 && $answer[0]['borrada'] = 1) ?  true : false; 

    }
  
    public function esPremium($idUsuario){
      $answer = $this->queryList("SELECT * FROM premium WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

      return (sizeof($answer) > 0 && $answer[0]['borrada']!= 1) ?  true : false; 

    }

}