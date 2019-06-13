<?php


class PDOUsuario extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }

    

    public function insertarUsuario(){
        

        $answer = $this->queryList("INSERT INTO usuario (nombre, apellido, email, password, tarjeta, creditos,fecha_nac, fecha_reg) VALUES (:nombre, :apellido, :email, :password, :tarjeta, :creditos, :fecha_nac, :fecha_reg);", array(':nombre' => $_POST['nombre-input-signup'], ':apellido' => $_POST['apellido-input-signup'],':email' => $_POST['email-input-signup'], ':password' => $_POST['password-input-signup'], ':tarjeta'=> $_POST['tarjeta-input-signup'], ':creditos'=> 2,':fecha_nac' => $_POST['fechanacimiento-input-signup'],':fecha_reg' =>  date_format( new DateTime("Now"),"y-m-d") ));
    }

    public function actualizarUsuario($idUsuario){
        
        $answer = $this->queryList("UPDATE usuario SET nombre=:nombre, apellido=:apellido, email=:email, password=:password, tarjeta=:tarjeta, fecha_nac=:fecha_nac WHERE idUsuario=:idUsuario", array(':idUsuario' => $idUsuario,':nombre' => $_POST['nombre-input-signup'],':apellido' => $_POST['apellido-input-signup'],':email' => $_POST['email-input-signup'],':password' => $_POST['password-input-signup'],':tarjeta' => $_POST['tarjeta-input-signup'],':fecha_nac' => $_POST['fechanacimiento-input-signup'],));
    }
    

    public function pasarAPremium($idUsuario){
    
        $answer = $this->queryList("INSERT INTO solicitud (idUsuario, idTipoSolicitud) VALUES (:idUsuario, :idTipoSolicitud);", array(':idUsuario' => $idUsuario, ':idTipoSolicitud' => 1));
    }

    public function pasarAEstandar($idUsuario){
        
        $answer = $this->queryList("INSERT INTO solicitud (idUsuario, idTipoSolicitud) VALUES (:idUsuario, :idTipoSolicitud);", array(':idUsuario' => $idUsuario, ':idTipoSolicitud' => 2));
    }

        

    public function traerUsuario($idUsuario){

        $answer = $this->queryList("SELECT * FROM usuario WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

        return (sizeof($answer)> 0) ? new Usuario($answer[0]['idUsuario'],$answer[0]['email'],$answer[0]['password'], $answer[0]['nombre'],$answer[0]['apellido'],$answer[0]['tarjeta'],(int)$answer[0]['creditos'],$answer[0]['fecha_nac'],$answer[0]['fecha_reg'],$answer[0]['borrada']) : false;

        /*$final_answer = [];
        foreach ($answer as &$element) {
            return new Usuario($element['idUsuario'],$element['email'],$element['password'], $element['nombre'],$element['apellido'],$element['tarjeta'],(int)$element['creditos'],$element['fecha_nac'],$element['fecha_reg'],$element['borrada']);

        }*/

   }

    public function traerUsuarioPorEmail($email){

        $answer = $this->queryList("SELECT * FROM usuario WHERE email=:email",array(':email'=> $email));
        

     return (sizeof($answer)> 0) ? new Usuario($answer[0]['idUsuario'],$answer[0]['email'],$answer[0]['password'], $answer[0]['nombre'],$answer[0]['apellido'],$answer[0]['tarjeta'],(int)$answer[0]['creditos'],$answer[0]['fecha_nac'],$answer[0]['fecha_reg'],$answer[0]['borrada']) : false;


   }

   public function existeEmail($email){

        $answer = $this->queryList("SELECT * FROM usuario WHERE email=:email" ,array(':email'=> $email));
        
     return (sizeof($answer)> 0) ? true : false;
   }


   public function existeSolicitud($idUsuario){

        $answer = $this->queryList("SELECT * FROM solicitud WHERE idUsuario=:idUsuario AND borrada=:borrada" ,array(':idUsuario'=> $idUsuario,':borrada'=> 0));
        
     return (sizeof($answer)> 0) ? true : false;
   }

   

  

   public function decrementarCreditos($idUsuario){
      $answer = $this->queryList("UPDATE usuario SET creditos = creditos - 1  WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

     }

    public function esPremium($idUsuario){
      $answer = $this->queryList("SELECT * FROM premium WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

      return (sizeof($answer) > 0 && $answer[0]['borrada']!= 1) ?  true : false; 


    }

}