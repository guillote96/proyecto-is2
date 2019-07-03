<?php

class PDODirecta extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }

  public function listarTodasDirectas(){

       $answer = $this->queryList("SELECT r.titulo,r.descripcion,d.precio,d.idResidenciaSemana,rs.idResidencia, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE d.borrada = 0 AND d.activa = 1", array());

        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = array("residenciasemana"=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null), "directa" => new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]), "titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }

    public function listarDirectas ($idResidencia){
    	//lista las semanas directas para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase Directa y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,d.idResidenciaSemana,rs.idResidencia,d.precio, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE rs.idResidencia = :idResidencia AND rs.borrada = 0",array(':idResidencia' => $idResidencia));

        $final_answer = [];
        foreach ($answer as &$element) {
        	$final_answer[] = array("residenciasemana"=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null),"directa"=> new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }


        public function listarDirectasFinalizadas (){
        //lista las semanas directas para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase Directa y ResidenciaSemana)
       $answer = $this->queryList("SELECT r.titulo,r.descripcion,d.idResidenciaSemana,rs.idResidencia,d.precio, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado,u.email,d.idPremiumCompra FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) inner join usuario u on (d.idPremiumCompra=u.idUsuario) WHERE  d.borrada = 1 AND d.idPremiumCompra is not null",array());

        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = array("residenciasemana"=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null),"directa"=> new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"], "email"=> $element["email"],"idPremium"=>$element["idPremiumCompra"]);
        }

        return $final_answer;

     }


     public function activarSemanaDirecta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE directa SET activa = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));


     }

     public function desactivarSemanaDirecta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE directa SET activa = 0 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));


     }

     public function borrarSemanaDirecta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE directa SET borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));


     }

      public function adjudicarDirecta($idResidenciaSemana,$idUser){
        $answer = $this->queryList("UPDATE directa SET idPremiumCompra=:idUser ,activa= 1,borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana, ':idUser'=>$idUser));



      }

      public function insertarDirecta($idResidenciaSemana,$precio){
        $answer = $this->queryList("INSERT INTO directa (idResidenciaSemana,precio,activa,borrada)  VALUES (:idResidenciaSemana,:precio,:activa,:borrada)",array(':idResidenciaSemana'=> $idResidenciaSemana, ':precio'=> $precio, ':activa'=> 0,':borrada'=> 0));



      }

     public function existeResidenciaSemanaDirecta($idResidenciaSemana){

    $answer = $this->queryList("SELECT * FROM directa WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana' => $idResidenciaSemana));
    if(sizeof($answer) > 0){
        return true;
    }
    return false;
    
   }


       public function buscarDirectas(){

         $sql="SELECT r.titulo, r.descripcion,d.idResidenciaSemana,rs.idResidencia,d.precio, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE d.activa=1 AND d.borrada = 0 AND";


         $parametros=array();
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

        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = array("residenciasemana"=>new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null),"directa"=> new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]),"titulo"=> $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }
     public function tieneComprador ($idResidenciaSemana){
        $answer = $this->queryList("SELECT * FROM directa WHERE idResidenciaSemana=:idResidenciaSemana", array(':idResidenciaSemana'=> $idResidenciaSemana));

        if(isset($answer[0]['idPremiumCompra']) && $answer[0]['idPremiumCompra'] != null){
            return true;
        }
        return false;


     }



}