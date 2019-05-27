<?php

class PDOSubasta extends PDORepository {

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

    public function subastaInfo($idRS){
        //subasta info activas ( activo =0)
        $answer = $this->queryList("SELECT su.idSubasta, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana) WHERE su.idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana' => $idRS));
        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"]);
        }

        return $final_answer;

    }


    public function pujaMaximaSubasta($idSubasta){

     $answer = $this->queryList("SELECT max(ps.puja) as puja, base FROM participa_subasta ps INNER JOIN subasta s ON (s.idSubasta=ps.idSubasta) WHERE s.idSubasta=:idSubasta",array(':idSubasta' => $idSubasta));
     if(!empty($answer[0]["puja"])){
        return $answer[0]["puja"];

      }
     else{
     
        return $answer[0]["base"];

        }

    }

    public function esMayorPuja($idSubasta, $puja){
        $answer = $this->queryList("SELECT * FROM participa_subasta ps WHERE idSubasta= :idSubasta AND ps.puja > :puja",array(':idSubasta' => $idSubasta,':puja' => $puja));

        if (sizeof($answer) > 0) {
            return false;
        }

        return true;
    }
   

     public function insertarParticipanteSubasta($idUsuario, $idSubasta, $puja){
        $answer= $this->queryList("INSERT INTO participa_subasta (idSubasta, idUsuario, puja)
VALUES (:idSubasta,:idUsuario, :puja);",array(':idUsuario'=> $idUsuario,':idSubasta' => $idSubasta,':puja' => $puja));

     }
    
    public function idUsuarioPujaMaximaSubasta($idSubasta){

     $answer = $this->queryList("SELECT ps.idUsuario FROM participa_subasta ps, subasta s WHERE s.idSubasta=:idSubasta AND (s.idSubasta=ps.idSubasta) order by puja desc",array(':idSubasta' => $idSubasta));
     if(!empty($answer)){
        return $answer[0]["idUsuario"];

      }
     return false;
 }



    public function traerResidenciaSemanasSubastas($idResidencia) {

          //Para una residencia trae unicamente las semanas que son SUBASTAS

         $answer = $this->queryList("SELECT rs.idResidenciaSemana, rs.idResidencia, rs.idSemana ,s.fecha_inicio , s.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia",array(':idResidencia' => $idResidencia));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"]);
         }

        return $final_answer;
      }


    public function traerSubastasInactivas($idResidencia) {

          //Para una residencia trae unicamente las semanas que  esta Inactivas

         $answer = $this->queryList("SELECT su.idSubasta, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia AND su.activa=:activa",array(':idResidencia' => $idResidencia, ':activa' => 0 ));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"]);
         }

        return $final_answer;
      }


      public function traerSubastasActivas($idResidencia){
        $answer = $this->queryList("SELECT su.idSubasta, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia AND su.activa=:activa",array(':idResidencia' => $idResidencia, ':activa' => 1 ));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"]);
         }

        return $final_answer;



      }

      public function activarSubasta($idSubasta){
        $answer = $this->queryList("UPDATE subasta SET activa= :activa WHERE idSubasta=:idSubasta",array(':idSubasta' => $idSubasta, ':activa' => 1 ));
        return true;
      }

  /**
   * Retorna todas las subastas de la base de datos
   *
   * @return array de objetos Subasta
   */
  public function getAuctions(){
    $auctions = $this->queryList("SELECT * FROM subasta", array());
    return array_map(function ($auction){
      return new Subasta (
        $auction["idSubasta"],
        $auction["idResidenciaSemana"],
        $auction["base"]
      );
    }, $auctions);
  }


}
