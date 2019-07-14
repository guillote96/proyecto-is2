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

        public function listarTodasSubasta (){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE s.activa=1 AND s.borrada = 0",array());

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array('residenciasemana' => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),'subasta' => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"],"pujamaxima"=>$this->pujaMaximaSubasta($element["idSubasta"]));
        }

        return $final_answer;

     }




    public function subastaInfo($idRS){
        //subasta info activas ( activo =1)
        $answer = $this->queryList("SELECT su.idSubasta,su.borrada, su.idResidenciaSemana, su.base, su.activa, s.fecha_inicio, s.fecha_fin FROM residencia_semana rs INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN subasta su ON (su.idResidenciaSemana=rs.idResidenciaSemana) WHERE su.idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana' => $idRS));
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

      public function actualizarBase($idResidenciaSemana,$base){
        $answer = $this->queryList("UPDATE subasta SET base=:base, activa=1 WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana' => $idResidenciaSemana, ':base' => $base ));
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
        SELECT s.idSubasta, s.base,s.borrada ,s.activa, r.titulo, sem.fecha_inicio, sem.fecha_fin
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
      $borrada= $auctionInfo["auction"]["borrada"];
      $base = $auctionInfo["auction"]["base"];
      $currentAmount = $auctionInfo["currentAmount"];
      $week = ["from_date" => $auctionInfo["auction"]["fecha_inicio"], "to_date" => $auctionInfo["auction"]["fecha_fin"]];
      $residence = $auctionInfo["auction"]["titulo"];


      return new AuctionDetail($auctionId, $active, $base, $currentAmount, $week, $residence,$borrada);
    }, $usersPerAuction);
  }


  public function insertarSubasta($idResidenciaSemana,$base){
    $answer = $this->queryList("INSERT INTO subasta (idResidenciaSemana,activa,base,borrada) VALUES (:idResidenciaSemana,0,:base,0);",array(':idResidenciaSemana' => $idResidenciaSemana,':base'=>$base));
   }



    public function listarSubasta ($idResidencia){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE rs.idResidencia = :idResidencia AND rs.borrada = 0 AND s.borrada = 0",array(':idResidencia' => $idResidencia));

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array('residenciasemana'=>new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),'subasta'=>new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
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

  public function listarSubastaInactivasSinMonto (){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE s.borrada = 0 AND s.activa=0 AND s.base is null",array());

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("residenciasemana" => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),"subasta" => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }

   public function buscarSubasta(){
         $sql="SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE (s.borrada = :borrada ) AND (s.activa=:activa) AND ";


         $parametros=array(':borrada' => 0,':activa'=> 1);
         if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin']) && !isset($_POST['localidad'])){
             return false;

         }
         if((empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) && empty($_POST['localidad'])){
              return false;

         }

          $fechainicio; $fechafin;
         if(isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio']) && isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])){

          $fechainicio=date_format(new DateTime($_POST['fecha_inicio']),"Y-m-d");
        $fechafin=date_format(new DateTime($_POST['fecha_fin']),"Y-m-d");

        $sql.=" (fecha_inicio BETWEEN '$fechainicio' AND '$fechafin') AND (fecha_fin BETWEEN '$fechainicio' AND '$fechafin') AND";
        


         }


          $ciudad;
         if(isset($_POST['localidad']) && !empty($_POST['localidad'])){
            $ciudad=$_POST['localidad'];
            $sql.=" r.ciudad LIKE '$ciudad%' AND";
         }
         $sql = substr($sql, 0, -4);

         
         $answer = $this->queryList($sql,$parametros);

      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("residenciasemana" => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),"subasta" => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }


         public function traerSubasta ($idResidenciaSemana){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) WHERE rs.idResidenciaSemana = :idResidenciaSemana AND rs.borrada = 0 AND s.borrada = 0",array(':idResidenciaSemana' => $idResidenciaSemana));

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array(new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]));
        }

        return $final_answer;

     }

       public function tieneMonto ($idResidenciaSemana){
       $answer = $this->queryList("SELECT * FROM subasta WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana' => $idResidenciaSemana));

         if(isset($answer[0]['base']) && $answer[0]['base'] != null){
            return true;
        }
        return false;
      }

  public function tieneParticipantesV2($idResidenciaSemana){
   $answer = $this->queryList("SELECT * FROM subasta s INNER JOIN participa_subasta ps ON(ps.idSubasta=s.idSubasta) WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));

        if(sizeof($answer) > 0){
            //existen participantes
          return true;
       }

       return false;


     }

     public function tieneGanador($idSubasta){
      $answer = $this->queryList("SELECT * FROM participa_subasta WHERE idSubasta=:idSubasta AND es_ganador is not null",array(':idSubasta'=> $idSubasta));

      if(sizeof($answer)> 0){

        return true;
      }
      
      return false;

 
     }

      public function listarSubastasFinalizadas(){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado, u.idUsuario, u.email FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) INNER JOIN participa_subasta ps ON (ps.idSubasta=s.idSubasta) INNER JOIN usuario u ON (u.idUsuario=ps.idUsuario) WHERE s.activa=0 AND s.borrada = 1 AND ps.es_ganador is NOT null",array());

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array('residenciasemana' => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),'subasta' => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"],"email"=>$element["email"],"idUsuario"=>$element["idUsuario"]);
        }

        return $final_answer;

     }

  public function partipantesSubasta($idSubasta){
    $answer = $this->queryList("SELECT idUsuario FROM participa_subasta WHERE idSubasta = :idSubasta ORDER BY puja DESC",array(':idSubasta'=> $idSubasta));

      return $answer;

   }


   public function buscarSubastaAdminActivas(){
         $sql="SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE (s.borrada = :borrada ) AND (s.activa=:activa) AND ";


         $parametros=array(':borrada' => 0,':activa'=> 1);
         if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin']) && !isset($_POST['residencia'])){
             return false;

         }
         if((empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) && empty($_POST['residencia'])){
              return false;

         }

          $fechainicio; $fechafin;
         if(isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio']) && isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])){

        $fechainicio=date_format(new DateTime($_POST['fecha_inicio']),"Y-m-d");
        $fechafin=date_format(new DateTime($_POST['fecha_fin']),"Y-m-d");

        $sql.=" (fecha_inicio BETWEEN '$fechainicio' AND '$fechafin') AND (fecha_fin BETWEEN '$fechainicio' AND '$fechafin') AND";
        


         }


          $residencia;
         if(isset($_POST['residencia']) && !empty($_POST['residencia'])){
            $residencia=$_POST['residencia'];
            $sql.=" r.titulo LIKE '$residencia%' AND";
         }
         $sql = substr($sql, 0, -4);

         
         $answer = $this->queryList($sql,$parametros);

      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("residenciasemana" => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),"subasta" => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }


     public function buscarSubastaAdminInactivasSinMonto(){
         $sql="SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE (s.borrada = :borrada ) AND (s.activa=:activa) AND s.base is null AND";


         $parametros=array(':borrada' => 0,':activa'=> 0);
         if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin']) && !isset($_POST['residencia'])){
             return false;

         }
         if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin'])){
             return false;

         }

          if(empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])){
              return false;

         }

          $fechainicio; $fechafin;
         if(isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio']) && isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])){

        $fechainicio=date_format(new DateTime($_POST['fecha_inicio']),"Y-m-d");
        $fechafin=date_format(new DateTime($_POST['fecha_fin']),"Y-m-d");

        $sql.=" (fecha_inicio BETWEEN '$fechainicio' AND '$fechafin') AND (fecha_fin BETWEEN '$fechainicio' AND '$fechafin') AND";
        


         }


          $residencia;
         if(isset($_POST['residencia']) && !empty($_POST['residencia'])){
            $residencia=$_POST['residencia'];
            $sql.=" r.titulo LIKE '$residencia%' AND";
         }
         $sql = substr($sql, 0, -4);

         
         $answer = $this->queryList($sql,$parametros);

      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array("residenciasemana" => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),"subasta" => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }

      public function buscarSubastaAdminFinalizadas(){

        $sql="SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado, u.idUsuario, u.email FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) INNER JOIN participa_subasta ps ON (ps.idSubasta=s.idSubasta) INNER JOIN usuario u ON (u.idUsuario=ps.idUsuario) WHERE s.activa=:activa AND s.borrada = :borrada AND ps.es_ganador is NOT null AND ";


         $parametros=array(':borrada' => 1,':activa'=> 0);
         if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin']) && !isset($_POST['residencia'])){
             return false;

         }
       if(!isset($_POST['fecha_inicio']) && !isset($_POST['fecha_fin'])){
             return false;

         }

          if(empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])){
              return false;

         }

          $fechainicio; $fechafin;
         if(isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio']) && isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])){

        $fechainicio=date_format(new DateTime($_POST['fecha_inicio']),"Y-m-d");
        $fechafin=date_format(new DateTime($_POST['fecha_fin']),"Y-m-d");

        $sql.=" (fecha_inicio BETWEEN '$fechainicio' AND '$fechafin') AND (fecha_fin BETWEEN '$fechainicio' AND '$fechafin') AND";
        


         }


          $residencia;
         if(isset($_POST['residencia']) && !empty($_POST['residencia'])){
            $residencia=$_POST['residencia'];
            $sql.=" r.titulo LIKE '$residencia%' AND";
         }
         $sql = substr($sql, 0, -4);

         
         $answer = $this->queryList($sql,$parametros);

      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)

      $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array('residenciasemana' => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),'subasta' => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"],"email"=>$element["email"],"idUsuario"=>$element["idUsuario"]);
        }

        return $final_answer;
     }




public function traerHistorialSubastas ($idUsuario){
      //lista las semanas en subasta para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase subasta y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,s.idSubasta,rs.borrada as rsborrada,s.borrada,s.base, s.idResidenciaSemana,rs.idResidencia,s.borrada, s.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN subasta s ON (rs.idResidenciaSemana=s.idResidenciaSemana) INNER JOIN participa_subasta ps ON (s.idSubasta=ps.idSubasta) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE s.borrada = 1 AND ps.idUsuario = :idUsuario AND ps.es_ganador = 1",array(':idUsuario' => $idUsuario));

        $final_answer = [];
        foreach ($answer as &$element) {
          $final_answer[] = array('residenciasemana' => new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element['rsborrada']),'subasta' => new Subasta ($element["idSubasta"],$element["idResidenciaSemana"], $element["base"],$element["activa"],$element["fecha_inicio"],$element["fecha_fin"],$element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"],"pujamaxima"=>$this->pujaMaximaSubasta($element["idSubasta"]));
        }

        return $final_answer;

     }






}
