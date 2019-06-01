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
        $answer = $this->queryList("SELECT su.idSubasta,su.borrada su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana) WHERE su.idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana' => $idRS));
        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]);
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

         $answer = $this->queryList("SELECT su.idSubasta,su.borrada, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia AND su.activa=:activa",array(':idResidencia' => $idResidencia, ':activa' => 0 ));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]);
         }

        return $final_answer;
      }


      public function traerSubastasActivas($idResidencia){
        $answer = $this->queryList("SELECT su.idSubasta,su.borrada, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana)WHERE idResidencia =:idResidencia AND su.activa=:activa",array(':idResidencia' => $idResidencia, ':activa' => 1 ));
         
         $final_answer = [];

         foreach ($answer as &$element) {
            $final_answer[] = new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]);
         }

        return $final_answer;



      }

      //Hcaer transicion de idSubasta a idResidenciaSemana

      public function activarSubasta($idSubasta){
        $answer = $this->queryList("UPDATE subasta SET activa= :activa WHERE idSubasta=:idSubasta",array(':idSubasta' => $idSubasta, ':activa' => 1 ));
        return true;
      }

      public function desactivarSubasta($idSubasta){
        $answer = $this->queryList("UPDATE subasta SET activa= :activa WHERE idSubasta=:idSubasta",array(':idSubasta' => $idSubasta, ':activa' => 0 ));
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


 public function getDetailedAuctions($activa)
  {
    $auctions = $this->queryList("
        SELECT s.idSubasta, s.base, s.activa, r.titulo, sem.fecha_inicio, sem.fecha_fin
        FROM subasta s
        INNER JOIN residencia_semana rs on s.idResidenciaSemana = rs.idResidenciaSemana
        INNER JOIN residencia r on rs.idResidencia = r.idResidencia
        INNER JOIN semana sem on rs.idSemana = sem.idSemana WHERE s.activa=$activa", []
    );


    $usersPerAuction = array_map(function ($auction) {
      $users = $this->queryList("
        SELECT u.idUsuario, ps.puja
        FROM usuario u
        INNER JOIN  participa_subasta ps on u.idUsuario = ps.idUsuario", []
      );

      $bids = array_map(function ($user) {return (float) $user["puja"];}, $users);
      $max = 0;
      foreach ($bids as $bid){
        if ($bid > $max){
          $max = $bid;
        }
      }
      $currentAmount = (float) $max;

      return [ "auction" => $auction, "users" => $users, "currentAmount" => $currentAmount];
    }, $auctions);

    return array_map(function ($auctionInfo) {

      $auctionId = $auctionInfo["auction"]["idSubasta"];
      $active = $auctionInfo["auction"]["activa"];
      $base = $auctionInfo["auction"]["base"];
      $currentAmount = $auctionInfo["currentAmount"];
      $week = ["from_date" => $auctionInfo["auction"]["fecha_inicio"], "to_date" => $auctionInfo["auction"]["fecha_fin"]];
      $residence = $auctionInfo["auction"]["titulo"];


      return new AuctionDetail($auctionId, $active, $base, $currentAmount, $week, $residence);
    }, $usersPerAuction);
  }


  public function insertarSubasta($idResidenciaSemana,$base){
    $answer = $this->queryList("INSERT INTO subasta (idResidenciaSemana,activa,base,borrada) VALUES (:idResidenciaSemana,0,:base,0);",array(':idResidenciaSemana' => $idResidenciaSemana,':base'=>$base));
   }



    public function listarSubasta ($idResidencia){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT s.idSubasta,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) WHERE rs.idResidencia = :idResidencia AND rs.borrada = 0 AND s.borrada = 0",array(':idResidencia' => $idResidencia));

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array(new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["estado"],null),new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]));
        }

        return $final_answer;

     }

     
     public function activarSemanaSubasta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE subasta SET activa = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));

     }

     public function desactivarSemanaSubasta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE subasta SET activa = 0 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));

     }

     public function borrarSemanaSubasta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE subasta SET borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));

     }

  public function tieneParticipantes($idSubasta){
   $answer = $this->queryList("SELECT * FROM participa_subasta WHERE idSubasta=:idSubasta",array(':idSubasta'=> $idSubasta));

        if(sizeof($answer) > 0){
            //existen participantes
          return true;
       }

       return false;


     }

  public function idUsuarioConMayorPujaEnSubasta($idSubasta){
    $answer = $this->queryList("SELECT idUsuario FROM participa_subasta WHERE idSubasta = :idSubasta ORDER BY puja DESC LIMIT 1",array(':idSubasta'=> $idSubasta));

    if(sizeof($answer)> 0){

      return $answer[0]["idUsuario"];
    }

    return false;

   }

    public function adjudicarSubasta($idSubasta, $idUsuario){

        $answer = $this->queryList("UPDATE participa_subasta SET es_ganador = 1 WHERE idSubasta= :idSubasta AND idUsuario=:idUsuario",array(':idSubasta'=> $idSubasta,':idUsuario'=> $idUsuario));

     }


}
