<?php


class Usuario extends PDORepository {
      public $idUsuario;
      public $email;
      public $password;
      public $nombre;
      public $apellido;
      public $tarjeta;
      public $creditos;
      public $fecha_nac;
      public $fecha_reg;
      public $borrada;




  public function __construct($idUsuario,$email,$password, $nombre,$apellido,$tarjeta,$creditos,$fecha_nac,$fecha_reg,$borrada){

    $this->idUsuario=$idUsuario;
    $this->email=$email;
    $this->password=$password;
    $this->nombre=$nombre;
    $this->apellido=$apellido;
    $this->tarjeta=$tarjeta;
    $this->creditos=$creditos;
    $this->fecha_nac=$fecha_nac;
    $this->fecha_reg=$fecha_reg;
    $this->borrada=$borrada;

  }

  public function getIdUsuario(){

    return $this->idUsuario;
  }



  public function getCreditos(){

    return $this->creditos;
  }

    public function getEmail(){

    return $this->email;
    }
  
    public function getPassword(){

    return $this->password;
  }





}