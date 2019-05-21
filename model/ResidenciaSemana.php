<?php

class ResidenciaSemana extends PDORepository {
    
    public $idResidenciaSemana;
    public $idResidencia;
    public $idSemana;
    public $fecha_inicio ;
    public $fecha_fin;
    public $estado;

    public function __construct($idResidenciaSemana,$idResidencia,$idSemana, $fecha_inicio,$fecha_fin,$estado) {
    	$this->idResidenciaSemana= $idResidenciaSemana;
    	$this->idResidencia= $idResidencia;
    	$this->idSemana= $idSemana;
    	$this->fecha_inicio= $fecha_inicio;
    	$this->fecha_fin= $fecha_fin;
    	$this->estado= $estado;
    }


}