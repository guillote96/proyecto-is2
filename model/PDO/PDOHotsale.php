<?php

class PDOHotsale extends PDORepository {

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

    public function insertarHotsale($idResidenciaSemana){
        $answer = $this->queryList("INSERT INTO hotsale (idResidenciaSemana,activa, borrada) VALUES (:idResidenciaSemana,0,0);",array(':idResidenciaSemana' => $idResidenciaSemana));
    }

    public function listarTodosHotsaleDeshabilitado(){
         //Solamente los que no estan habilitados y no estan borrados (posibles Hotsale)
        $answer = $this->queryList("SELECT r.titulo, r.descripcion,h.idResidenciaSemana, h.activa,h.idUsuario,h.borrada as hotsaleborrada,h.precio,s.fecha_inicio,s.fecha_fin,rs.idResidencia,rs.idSemana,rs.estado,rs.borrada FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN hotsale h ON (h.idResidenciaSemana=rs.idResidenciaSemana) WHERE h.activa=0 AND h.borrada=0",array());
        $final_answer = [];
        foreach ($answer as &$element) {
         $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]),'hotsale'=> new Hotsale ($element["idResidenciaSemana"], $element["idUsuario"],$element["precio"],$element["fecha_inicio"],$element["fecha_fin"],$element["activa"],$element["hotsaleborrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);

        }



       return $final_answer;
     }


     public function habilitarHotsale($idResidenciaSemana,$precio){
        $answer = $this->queryList("UPDATE hotsale SET activa=:activa, precio=:precio WHERE idResidenciaSemana=:idResidenciaSemana",array(':idResidenciaSemana' => $idResidenciaSemana, ':activa' => 1,':precio'=>$precio));
           return true;



     }

}