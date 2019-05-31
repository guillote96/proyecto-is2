<?php

class ResidenciaSemana extends PDORepository {
    
    public $idResidenciaSemana;
    public $idResidencia;
    public $idSemana;
    public $fecha_inicio ;
    public $fecha_fin;
    public $estado;
     public $borrada;

    public function __construct($idResidenciaSemana,$idResidencia,$idSemana, $fecha_inicio,$fecha_fin,$estado,$borrada) {
    	$this->idResidenciaSemana= $idResidenciaSemana;
    	$this->idResidencia= $idResidencia;
    	$this->idSemana= $idSemana;
    	$this->fecha_inicio= $fecha_inicio;
    	$this->fecha_fin= $fecha_fin;
    	$this->estado= $estado;
        $this->borrada= $borrada;
    }


    public function getIdResidenciaSemana(){
        return $this->idResidenciaSemana;
    }
    public function getIdResidencia(){
        return $this->idResidencia;
    }

    public function getIdSemana(){
        return $this->idSemana;
    }

    public function getFechaInicio(){
        return $this->fecha_inicio;
    }

    public function getFechaFin(){
        return $this->fecha_fin;
    }

    public function getEstado(){
        return $this->estado;
    }

     public function getBorrada(){
        return $this->borrada;
    }




}