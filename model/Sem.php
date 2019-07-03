<?php

class Sem extends PDORepository {
    
    public $idSemana;
    public $fecha_inicio ;
    public $fecha_fin;
    public $idAdministrador;
    public $fecha_creacion;

    public function __construct($idSemana, $fecha_inicio, $fecha_fin, $idAdministrador, $fecha_creacion) {
    	$this->idSemana= $idSemana;
    	$this->fecha_inicio=  $fecha_inicio;
    	$this->fecha_fin= $fecha_fin;
    	$this->idAdministrador= $idAdministrador;
    	$this->fecha_creacion= $fecha_creacion;
    }


    public function getIdSemana(){

        return $this->idSemana;
    }





}