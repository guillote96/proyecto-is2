<?php

class Directa extends PDORepository {
    
    public $idResidenciaSemana;
    public $idPremiumCompra;
    public $activa;
    public $borrada;


    public function __construct($idResidenciaSemana,$idPremiumCompra, $activa, $borrada) {
    	$this->idResidenciaSemana= $idResidenciaSemana;
    	$this->idPremiumCompra= $idPremiumCompra;
    	$this->activa= $activa;
    	$this->borrada= $borrada;        
    }

    public function getIdResidenciaSemana(){
        return $this->idResidenciaSemana;
    }

    public function getIdPremiumCompra(){
        return $this->idPremiumCompra;
    }

    public function getActiva(){
        return $this->activa;
    }

    public function getBorrada(){
        return $this->borrada;
    }









}