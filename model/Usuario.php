<?php

class Usuario extends PDOUsuario {
    
    public $idUsuario;
    public $nombre;
    public $apellido;
    public $email;
    public $contraseña;
    public $tarjeta;
    public $fecha;
    public $creditos;
    

    public function __construct($idUsuario,$nombre,$apellido,$email,$contraseña,$tarjeta,$fecha,$creditos) {

        $this->idUsuario= $idUsuario;
    	$this->nombre= $nombre;
    	$this->apellido= $apellido;
    	$this->email= $email;
    	$this->contraseña= $contraseña;
    	$this->tarjeta= $tarjeta;
    	$this->fecha= $fecha;
        $this->creditos=$creditos;
        
    }

    public function getIdUsuario(){
    	return $this->idUsuario;
    }

    public function getNombre(){
    	return $this->nombre;
    }
    public function getApelido(){
    	return $this->apellido
        ;
    }
    public function getEmail(){
    	return $this->email;
    }
    
    public function getContraseña(){
    	return $this->contraseña;
    }
    
    public function getTarjeta(){
    	return $this->tarjeta;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function getCreditos(){
        return $this->creditos;
    }
    



}