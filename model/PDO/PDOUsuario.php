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
    public function listarUsuarios(){
      $answer = $this->queryList("SELECT * FROM usuario WHERE borrada=:borrada",array(':borrada'=>0));

       $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("usuario"=> new Usuario($element['idUsuario'],$element['email'],$element['password'], $element['nombre'],$element['apellido'],$element['tarjeta'],(int)$element['creditos'],$element['fecha_nac'],$element['fecha_reg'],$element['borrada']),'esPremium' => $this->esPremium($element['idUsuario']));
        }

        return $final_answer;


    }

    

    public function insertarUsuario(){
        

        $answer = $this->queryList("INSERT INTO usuario (nombre, apellido, email, password, tarjeta, creditos,fecha_nac, fecha_reg) VALUES (:nombre, :apellido, :email, :password, :tarjeta, :creditos, :fecha_nac, :fecha_reg);", array(':nombre' => $_POST['nombre-input-signup'], ':apellido' => $_POST['apellido-input-signup'],':email' => $_POST['email-input-signup'], ':password' => $_POST['password-input-signup'], ':tarjeta'=> $_POST['tarjeta-input-signup'], ':creditos'=> 2,':fecha_nac' => $_POST['fechanacimiento-input-signup'],':fecha_reg' =>  date_format( new DateTime("Now"),"y-m-d") ));
    }

    public function actualizarUsuario($idUsuario){
        
        $answer = $this->queryList("UPDATE usuario SET nombre=:nombre, apellido=:apellido, email=:email, password=:password, tarjeta=:tarjeta, fecha_nac=:fecha_nac WHERE idUsuario=:idUsuario", array(':idUsuario' => $idUsuario,':nombre' => $_POST['nombre-input-signup'],':apellido' => $_POST['apellido-input-signup'],':email' => $_POST['email-input-signup'],':password' => $_POST['password-input-signup'],':tarjeta' => $_POST['tarjeta-input-signup'],':fecha_nac' => $_POST['fechanacimiento-input-signup'],));
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

  

   public function decrementarCreditos($idUsuario){
      $answer = $this->queryList("UPDATE usuario SET creditos = creditos - 1  WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

     }

    public function esPremium($idUsuario){
      $answer = $this->queryList("SELECT * FROM premium WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

      return (sizeof($answer) > 0 && $answer[0]['borrada']!= 1) ?  true : false; 


    }

    public function buscarUsuario(){
      $sql="SELECT * FROM usuario WHERE borrada=:borrada AND ";
      $parametros=array(':borrada'=>0);
         if(!isset($_POST['nombre']) && !isset($_POST['fecha_registro']) && !isset($_POST['tipo_usuario'])){
             return false;

         }
         $nombre;

         if(isset($_POST['nombre']) && !empty($_POST['nombre'])){
             $nombre=$_POST['nombre'];
            $sql.="nombre LIKE '$nombre%' AND ";
            
         }

         if(isset($_POST['fecha_registro']) && !empty($_POST['fecha_registro'])){
            $sql.="fecha_reg=:fecha_registro AND ";
            $parametros[":fecha_registro"]=$_POST['fecha_registro'];
         }
         $sql = substr($sql, 0, -5);
         $answer = $this->queryList($sql,$parametros);
         $premium;
         if(isset($_POST['tipo_usuario']) && !empty($_POST['tipo_usuario'])){
             if($_POST['tipo_usuario'] == 1){
              //estndar
                $premium=false;
             }
             if($_POST['tipo_usuario'] == 5){
                $premium=true;
             }

         }

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("usuario"=> new Usuario($element['idUsuario'],$element['email'],$element['password'], $element['nombre'],$element['apellido'],$element['tarjeta'],(int)$element['creditos'],$element['fecha_nac'],$element['fecha_reg'],$element['borrada']),'esPremium' => $this->esPremium($element['idUsuario']));
         }



        if(!isset($premium)){
          return $final_answer;
        }

         $tipo=[];
         if((isset($premium)) && ($premium)){
             
              foreach ($final_answer as $key => $cliente) {
                if($cliente['esPremium']){
                  $tipo[]=$cliente;
                }
              }

                      

        }else{
               
               foreach ($final_answer as $key => $cliente) {
                 if(!$cliente['esPremium']){
                  $tipo[]=$cliente;
                 }

               }

        }    


      return $tipo;


    }

}