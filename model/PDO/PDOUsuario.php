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

    public function listarTodas() {
        $answer = $this->queryList("SELECT * FROM residencia",array());
        $final_answer = [];
        foreach ($answer as &$element) {
            $tieneparticipantes=$this->existenParticipantes($element["idResidencia"]);
            $final_answer[] = new Residencia ($element["idResidencia"],$element["ciudad"], $element["direccion"],$element["idAdministrador"],$element["titulo"],$element["provincia"],$element["partido"],$element["descripcion"],$tieneparticipantes);
        }

        return $final_answer;
    }

    public function insertarUsuario(){
        

        $answer = $this->queryList("INSERT INTO usuario (nombre, apellido, email, password, tarjeta, creditos,fecha_nac, fecha_reg) VALUES (:nombre, :apellido, :email, :password, :tarjeta, :creditos, :fecha_nac, :fecha_reg);", array(':nombre' => $_POST['nombre-input-signup'], ':apellido' => $_POST['apellido-input-signup'],':email' => $_POST['email-input-signup'], ':password' => $_POST['password-input-signup'], ':tarjeta'=> $_POST['tarjeta-input-signup'], ':creditos'=> 2,':fecha_nac' => $_POST['fechanacimiento-input-signup'],':fecha_reg' =>  date_format( new DateTime("Now"),"y-m-d") ));
    }

        public function traerResidencia($idResidencia) {
        $answer = $this->queryList("SELECT * FROM residencia WHERE idResidencia=:idResidencia",array(':idResidencia' => $idResidencia));
        $final_answer = [];
        foreach ($answer as &$element) {
             $tieneparticipantes=$this->existenParticipantes($element["idResidencia"]);
            $final_answer[] = new Residencia ($element["idResidencia"],$element["ciudad"], $element["direccion"],$element["idAdministrador"],$element["titulo"],$element["provincia"],$element["partido"],$element["descripcion"],$tieneparticipantes);
        }

        return $final_answer;
    }


     public function traerResidenciaSemanas($idResidencia) {
         $answer = $this->queryList("SELECT * FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) WHERE idResidencia=:idResidencia",array(':idResidencia' => $idResidencia));
         $final_answer = [];
                foreach ($answer as &$element) {


                }

        return $final_answer;
    }

         public function existenParticipantes($idResidencia){

       $partDirectas = $this->queryList("SELECT * FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) WHERE rs.idResidencia=:idResidencia", array(':idResidencia' => $idResidencia));

       $partSubasta = $this->queryList("SELECT * FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN participa_subasta ps ON(ps.idSubasta=s.idSubasta) WHERE rs.idResidencia=:idResidencia", array(':idResidencia' => $idResidencia));
         
       $partHotsale = $this->queryList("SELECT * FROM residencia_semana rs INNER JOIN hotsale h ON (rs.idResidenciaSemana=h.idResidenciaSemana) WHERE rs.idResidencia=:idResidencia", array(':idResidencia' => $idResidencia));


       if((sizeof($partDirectas) > 0 ) || (sizeof($partSubasta) > 0 ) || (sizeof($partHotsale) > 0 )){
            //existen participantes
          return true;
       }

       return false;
     }


     public function actualizarResidencia($idResidencia){
       $answer = $this->queryList("UPDATE residencia SET titulo= :titulo, direccion=:direccion, ciudad=:ciudad, partido=:partido, provincia=:provincia, descripcion=:descripcion WHERE idResidencia=:idResidencia",array(':idResidencia' => $idResidencia, ':titulo' => $_POST['titulo'], ':direccion' => $_POST['direccion'], ':ciudad' => $_POST['idLocalidad'], ':partido' => $_POST['idPartido'], ':provincia' => $_POST['idProvincia'], ':descripcion' => $_POST['descripcion']));
  
     }

    public function borrarResidencia($idResidencia) {

      
        $answer = $this->queryList("DELETE FROM residencia WHERE idResidencia=:idResidencia",array(':idResidencia' => $idResidencia));
            

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

   public function decrementarCreditos($idUsuario){
      $answer = $this->queryList("UPDATE usuario SET creditos = creditos - 1  WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

     }

    public function esPremium($idUsuario){
      $answer = $this->queryList("SELECT * FROM premium WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

      return (sizeof($answer) > 0 && $answer[0]['borrada']!= 1) ?  true : false; 


    }

}