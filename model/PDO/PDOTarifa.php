<?php

class PDOTarifa extends PDORepository {

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


    public function traerTarifaEstandar(){

        $answer = $this->queryList("SELECT * FROM tarifa WHERE idTarifa=:idTarifa",array(':idTarifa'=> 2 ));

        return $answer[0]['monto'];

}

    public function traerTarifaPremium(){

            $answer = $this->queryList("SELECT * FROM tarifa WHERE idTarifa=:idTarifa",array(':idTarifa'=> 1 ));

        return $answer[0]['monto'];
    }


public function actualizarTarifaPremium($monto){

            $answer = $this->queryList("UPDATE tarifa SET monto = $monto WHERE idTarifa=:idTarifa",array(':idTarifa'=> 1 ));

        return true;
    }

public function actualizarTarifaEstandar($monto){

            $answer = $this->queryList("UPDATE tarifa SET monto = $monto WHERE idTarifa=:idTarifa",array(':idTarifa'=> 2 ));
            
        return true;
    }   



}