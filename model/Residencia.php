<?php

class Residencia extends PDORepository {
    
    public $idResidencia;
    public $ciudad;
    public $direccion;
    public $idAdministrador;
    public $titulo;
    public $provincia;
    public $partido; 
    public $descripcion;
    public $tieneparticipantes;

    public function __construct($idResidencia,$ciudad,$direccion,$idAdministrador,$titulo,$provincia,$partido,$descripcion,$tieneparticipantes) {
    	$this->idResidencia= $idResidencia;
    	$this->ciudad= $ciudad;
    	$this->direccion= $direccion;
    	$this->idAdministrador= $idAdministrador;
    	$this->titulo= $titulo;
    	$this->provincia= $provincia;
        $this->partido= $partido;
        $this->descripcion= $descripcion;
        $this->tieneparticipantes=$tieneparticipantes;
        
    }

    public function getIdResidencia(){
    	return $this->idResidencia;
    }

    public function getCiudad(){
    	return $this->ciudad;
    }
    public function getDireccion(){
    	return $this->direccion;
    }
    public function getIdAdministrador(){
    	return $this->idAdministrador;
    }
    
    public function getTitulo(){
    	return $this->titulo;
    }
    
    public function getProvincia(){
    	return $this->provincia;
    }

    public function getPartido(){
        return $this->partido;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }
    



}