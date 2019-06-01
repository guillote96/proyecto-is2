<?php

class PDOHotsale extends PDORepository {

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

    public function insertarHotsale($idResidenciaSemana){
        $answer = $this->queryList("INSERT INTO hotsale (idResidenciaSemana,activa, borrada) VALUES (:idResidenciaSemana,0,0);",array(':idResidenciaSemana' => $idResidenciaSemana));
      }






}