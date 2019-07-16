<?php


class PDOAdmin extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }


    public function listarAdministradores(){
      $answer = $this->queryList("SELECT * FROM administrador WHERE borrada=0",array());

       $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("admin"=> new Administrador($element['idAdministrador'],$element['nombre'],$element['apellido'],$element['username'],$element['password'], $element['dni'], $element['borrada']));
      }

        return $final_answer;


    }


       public function insertarAdmin(){
        

        $answer = $this->queryList("INSERT INTO administrador (nombre, apellido, username, password, dni) VALUES (:nombre, :apellido, :username, :password, :dni);", array(':nombre' => $_POST['nombre-input-signup'], ':apellido' => $_POST['apellido-input-signup'],':username' => $_POST['username-input-signup'], ':password' => $_POST['password-input-signup'], ':dni'=> $_POST['dni-input-signup']));
    }


     public function existeUsername($username){

        $answer = $this->queryList("SELECT * FROM administrador WHERE username=:username" ,array(':username'=> $username));
        
     return (sizeof($answer)> 0) ? true : false;
   }

   public function hayMasAdministradores(){

        $answer = $this->queryList("SELECT * FROM administrador WHERE borrada=0",array());
        
     return (sizeof($answer)>0) ? true : false;
   }


       public function traerAdminPorUsername($username){

        $answer = $this->queryList("SELECT * FROM administrador WHERE username=:username AND borrada=0",array(':username'=> $username));
        

     return (sizeof($answer)> 0) ? new Administrador($answer[0]['idAdministrador'],$answer[0]['nombre'],$answer[0]['apellido'], $answer[0]['username'],$answer[0]['password'],$answer[0]['dni'],$answer[0]['borrada']) : false;


   }


public function desactivarCuenta($idAdministrador){
      $answer = $this->queryList("UPDATE administrador SET borrada=:borrada WHERE idAdministrador=:idAdministrador",array(':idAdministrador'=> $idAdministrador,':borrada'=>1));

     }

public function reActivarCuenta($idAdministrador){
      $answer = $this->queryList("UPDATE administrador SET borrada=:borrada WHERE idAdministrador=:idAdministrador",array(':idAdministrador'=> $idAdministrador,':borrada'=>0));

     }
}