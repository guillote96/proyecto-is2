<?php


class Administrador extends PDORepository {
      public $idAdministrador;
      public $nombre;
      public $apellido;
      public $username;
      public $password;
      public $dni;
      public $borrada;





  public function __construct($idAdministrador,$nombre,$apellido,$username,$password, $dni,$borrada){

    $this->idAdministrador=$idAdministrador;
    $this->nombre=$nombre;
    $this->apellido=$apellido;
    $this->username=$username;
    $this->password=$password;
    $this->dni=$dni;
    $this->borrada=$borrada;

  }

  public function getIdAdmin(){

    return $this->idAdministrador;
  }



    public function getUsername(){

    return $this->username;
    }
  
    public function getPassword(){

    return $this->password;
  }

  public function getDni(){

      return $this->dni;
    }


  public function getBorrada(){
      return $this->borrada;


    }


}