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

    public function envioPasarAPremium($idUsuario){
    
        $answer = $this->queryList("INSERT INTO solicitud (idUsuario, idTipoSolicitud) VALUES (:idUsuario, :idTipoSolicitud);", array(':idUsuario' => $idUsuario, ':idTipoSolicitud' => 1));
    }

    public function envioPasarAEstandar($idUsuario){
        
        $answer = $this->queryList("INSERT INTO solicitud (idUsuario, idTipoSolicitud) VALUES (:idUsuario, :idTipoSolicitud);", array(':idUsuario' => $idUsuario, ':idTipoSolicitud' => 2));
    }


    public function existeSolicitud($idUsuario){

        $answer = $this->queryList("SELECT * FROM solicitud WHERE idUsuario=:idUsuario AND borrada=:borrada AND aceptada=:aceptada" ,array(':idUsuario'=> $idUsuario,':borrada'=> 0,':aceptada'=> 0));
        
     return (sizeof($answer)> 0) ? true : false;
   }   



    public function actualizarSolicitud($idUsuario){
        
        $answer = $this->queryList("UPDATE solicitud SET aceptada = 1 WHERE idUsuario=:idUsuario AND borrada=:borrada" ,array(':idUsuario'=> $idUsuario,':borrada'=> 0));
        
    }









}