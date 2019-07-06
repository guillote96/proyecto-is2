<?php

class PDOResidenciaSemana extends PDORepository {

	//IMPORTANTE: La clase esta esta armada con cruce de datos entre residencia y semana

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }



     public function traerResidenciaSemanas($idResidencia) {
     	//Para una residencia trae TODAS las semanas que intervengan en la misma
         $answer = $this->queryList("SELECT rs.borrada,rs.idResidenciaSemana, rs.idResidencia,rs.idSemana ,s.fecha_inicio , s.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) WHERE idResidencia =:idResidencia",array(':idResidencia' => $idResidencia));
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]);
         }

        return $final_answer;
    }

         public function traerResidenciaSemanasSubastas($idResidencia) {

         	//Para una residencia trae unicamente las semanas que son SUBASTAS

         $answer = $this->queryList("SELECT rs.idResidenciaSemana, rs.idResidencia,rs.idSemana ,s.fecha_inicio , s.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia",array(':idResidencia' => $idResidencia));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]);
         }

        return $final_answer;
    }
    

     public function traerResidenciaSemanasSubastasDeParticipanteActivas($idUsuario) {

            //Para una residencia trae unicamente las semanas que son SUBASTAS

         $answer = $this->queryList("SELECT r.idResidencia,rs.borrada,s.idSemana,su.idSubasta,r.titulo, r.descripcion, s.fecha_inicio, s.fecha_fin, rs.idResidenciaSemana,max(ps.puja) as puja, rs.estado, su.activa FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) inner join semana s on (s.idSemana=rs.idSemana) inner JOIN subasta su on (su.idResidenciaSemana =rs.idResidenciaSemana) inner JOIN participa_subasta ps ON (ps.idSubasta = su.idSubasta)  where ps.idUsuario = :idUsuario AND su.activa = 1 GROUP BY ps.idSubasta",array(':idUsuario' => $idUsuario));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $residencia= PDOResidencia::getInstance()->traerResidencia($element["idResidencia"]);
            $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]), 'residencia' => $residencia, 'pujamaxima' => $element["puja"], "idSubasta" => $element["idSubasta"]);
         }

        return $final_answer;
    }

   public function insertarSemanaResidencia($idResidencia,$idSemana){
     $answer = $this->queryList("INSERT INTO residencia_semana (idResidencia,idSemana,borrada)
     VALUES (:idResidencia,:idSemana,:borrada);",array(':idResidencia' => $idResidencia,':idSemana'=>$idSemana,':borrada'=>0));



   }

   public function existeSemanaParaResidencia($idResidencia,$idSemana){

    $answer = $this->queryList("SELECT * FROM residencia_semana WHERE idResidencia=:idResidencia AND idSemana =:idSemana",array(':idResidencia' => $idResidencia,':idSemana'=> $idSemana));
    if(sizeof($answer) > 0){
        return true;
    }
    return false;
    
   }

   public function traerIdResidenciaSemana($idResidencia,$idSemana){
    $answer = $this->queryList("SELECT rs.idResidenciaSemana FROM residencia_semana rs WHERE idResidencia=:idResidencia AND idSemana =:idSemana",array(':idResidencia' => $idResidencia,':idSemana'=> $idSemana));

    return $answer[0]["idResidenciaSemana"];





   }

 public function actualizarResidenciaSemana($idResidenciaSemana,$idSemana){

    $answer = $this->queryList("UPDATE residencia_semana SET idSemana=:idSemana WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana' => $idResidenciaSemana,':idSemana'=> $idSemana));



 } 

}