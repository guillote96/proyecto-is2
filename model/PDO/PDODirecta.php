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

    public function listarDirectas ($idResidencia){
    	//lista las semanas directas para una residencia determinada (devuelve un arreglo con arreglos de 2 objetos de la clase Directa y ResidenciaSemana)
       $answer = $this->queryList("SELECT d.idResidenciaSemana,rs.idResidencia, d.idPremiumCompra,d.borrada, d.activa,rs.idSemana, sem.fecha_inicio, sem.fecha_fin, rs.estado FROM residencia_semana rs INNER JOIN directa d ON (rs.idResidenciaSemana=d.idResidenciaSemana) INNER JOIN semana sem ON (sem.idSemana= rs.idSemana) WHERE rs.idResidencia = :idResidencia AND rs.borrada = 0 AND d.borrada = 0",array(':idResidencia' => $idResidencia));

        $final_answer = [];
        foreach ($answer as &$element) {
        	$final_answer[] = array(new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["estado"],null), new Directa($element["idResidenciaSemana"],$element["idPremiumCompra"], $element["activa"], $element["borrada"]));
        }

        return $final_answer;

     }


     public function activarSemanaDirecta($idResidenciaSemana){

        $answer = $this->queryList("UPDATE directa SET activa = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));


     }



}