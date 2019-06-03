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

       $answer = $this->queryList("SELECT r.titulo,r.descripcion,d.precio,d.idResidenciaSemana,rs.idResidencia, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) INNER JOIN residencia r ON (r.idResidencia=rs.idResidencia) WHERE rs.borrada = 0 AND d.borrada = 0 AND d.activa = 1", array());

        $final_answer = [];
        foreach ($answer as &$element) {
            $final_answer[] = array("residenciasemana"=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null), "directa" => new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]), "titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);
        }

        return $final_answer;

     }

    public function listarDirectas ($idResidencia){
    	//lista las semanas directas para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase Directa y ResidenciaSemana)
       $answer = $this->queryList("SELECT d.idResidenciaSemana,rs.idResidencia,d.precio, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) WHERE rs.idResidencia = :idResidencia AND rs.borrada = 0 AND d.borrada = 0",array(':idResidencia' => $idResidencia));

        $final_answer = [];
        foreach ($answer as &$element) {
        	$final_answer[] = array(new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],null), new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"],$element["precio"], $element["activa"], $element["borrada"]));
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
        $answer = $this->queryList("UPDATE directa SET idPremiumCompra=:idUser ,activa= 0,borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana, ':idUser'=>$idUser));



      }



}