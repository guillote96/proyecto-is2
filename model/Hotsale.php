<?php

class Hotsale extends PDORepository {

    
    public $idResidenciaSemana;
    public $idUsuario;
    public $precio;
    public $fecha_inicio;
    public $fecha_fin;
    public $activa;
    public $borrada;


    public function __construct($idResidenciaSemana,$idUsuario,$precio,$fecha_inicio,$fecha_fin, $activa, $borrada) {
    	$this->idResidenciaSemana= $idResidenciaSemana;
    	$this->idUsuario= $idUsuario;
        $this->precio= $precio;
        $this->fecha_inicio= $fecha_inicio;
        $this->fecha_fin=$fecha_fin;
        $this->activa= $activa;
    	$this->borrada= $borrada;        
    }

    public function getIdResidenciaSemana(){
        return $this->idResidenciaSemana;
    }

    public function getIdUsuario(){
        return $this->idUsuario;
    }
     public function getPrecio(){
        return $this->precio;
    }
    public function getActiva(){
        return $this->activa;
    }

    public function getBorrada(){
        return $this->borrada;
    }

}


