<?php

class Subasta extends PDORepository {
    
    public $idSubasta;
    public $idResidenciaSemana;
    public $base;
    public $activa;
    public $fecha_inicio;
    public $fecha_fin;
    public $borrada;

    /*public function __construct($idSubasta,$idResidenciaSemana,$base,$activa) {
    	$this->idSubasta = $idSubasta;
    	$this->idResidenciaSemana = $idResidenciaSemana;
    	$this->base = $base;
        $this->activa= $activa;       
    }*/

   public function __construct($idSubasta,$idResidenciaSemana,$base,$activa, $fecha_inicio, $fecha_fin,$borrada) {
        $this->idSubasta = $idSubasta;
        $this->idResidenciaSemana = $idResidenciaSemana;
        $this->base = $base;
        $this->activa= $activa;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin= $fecha_fin;
        $this->borrada= $borrada;        
    }


    public function getIdSubasta(){
        return $this->idSubasta;
    }

    public function getIdResidenciaSemana(){
        return $this->idResidenciaSemana;
    }
    public function getBase(){
        return $this->base;
    }
    public function getFechaInicio(){
        return $this->fecha_inicio;
    }

    public function getFechaFin(){
        return $this->fecha_fin;
    }

    public function getActiva(){

           return $this->activa;
    }
    public function getBorrada(){

           return $this->borrada;
    }

}
