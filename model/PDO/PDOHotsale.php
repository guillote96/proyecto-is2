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

        public function listarTodosHotsale(){
         //Solamente los que no estan habilitados y no estan borrados (posibles Hotsale)
        $answer = $this->queryList("SELECT r.titulo, r.descripcion,h.idResidenciaSemana, h.activa,h.idUsuario,h.borrada as hotsaleborrada,h.precio,s.fecha_inicio,s.fecha_fin,rs.idResidencia,rs.idSemana,rs.estado,rs.borrada FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN hotsale h ON (h.idResidenciaSemana=rs.idResidenciaSemana) WHERE h.activa=1 AND h.borrada=0",array());
        $final_answer = [];
        foreach ($answer as &$element) {
         $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]),'hotsale'=> new Hotsale ($element["idResidenciaSemana"], $element["idUsuario"],$element["precio"],$element["fecha_inicio"],$element["fecha_fin"],$element["activa"],$element["hotsaleborrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);

        }

        return $final_answer;

    }


         public function listarHotsale($idResidencia){
         //Solamente los que no estan habilitados y no estan borrados (posibles Hotsale)
        $answer = $this->queryList("SELECT r.titulo, r.descripcion,h.idResidenciaSemana, h.activa,h.idUsuario,h.borrada as hotsaleborrada,h.precio,s.fecha_inicio,s.fecha_fin,rs.idResidencia,rs.idSemana,rs.estado,rs.borrada FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN hotsale h ON (h.idResidenciaSemana=rs.idResidenciaSemana) WHERE r.idResidencia=:idResidencia",array(':idResidencia'=>$idResidencia));
        $final_answer = [];
        foreach ($answer as &$element) {
         $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]),'hotsale'=> new Hotsale ($element["idResidenciaSemana"], $element["idUsuario"],$element["precio"],$element["fecha_inicio"],$element["fecha_fin"],$element["activa"],$element["hotsaleborrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);

        }



       return $final_answer;
     }

             public function listarHotsaleFinalizados(){
        $answer = $this->queryList("SELECT u.email,r.titulo, r.descripcion,h.idResidenciaSemana, h.activa,h.idUsuario,h.borrada as hotsaleborrada,h.precio,s.fecha_inicio,s.fecha_fin,rs.idResidencia,rs.idSemana,rs.estado,rs.borrada FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN hotsale h ON (h.idResidenciaSemana=rs.idResidenciaSemana) INNER JOIN usuario u ON (u.idUsuario=h.idUsuario) WHERE h.activa=0 AND h.borrada=1 AND h.idUsuario is not null",array());
        $final_answer = [];
        foreach ($answer as &$element) {
         $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]),'hotsale'=> new Hotsale ($element["idResidenciaSemana"], $element["idUsuario"],$element["precio"],$element["fecha_inicio"],$element["fecha_fin"],$element["activa"],$element["hotsaleborrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"],"email"=> $element["email"],"idUsuario"=> $element["idUsuario"]);

        }



       return $final_answer;
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


  public function buscarHotsales(){

         $sql="SELECT r.titulo, r.descripcion,h.idResidenciaSemana, h.activa,h.idUsuario,h.borrada as hotsaleborrada,h.precio,s.fecha_inicio,s.fecha_fin,rs.idResidencia,rs.idSemana,rs.estado,rs.borrada FROM residencia r INNER JOIN residencia_semana rs ON (r.idResidencia=rs.idResidencia) INNER JOIN  semana s ON (rs.idSemana=s.idSemana) INNER JOIN hotsale h ON (h.idResidenciaSemana=rs.idResidenciaSemana) WHERE h.activa=1 AND h.borrada=0 AND";


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
         $final_answer[] = array('residenciasemana'=> new ResidenciaSemana ($element["idResidenciaSemana"],$element["idResidencia"], $element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["estado"],$element["borrada"]),'hotsale'=> new Hotsale ($element["idResidenciaSemana"], $element["idUsuario"],$element["precio"],$element["fecha_inicio"],$element["fecha_fin"],$element["activa"],$element["hotsaleborrada"]),"titulo" => $element["titulo"],"descripcion"=> $element["descripcion"]);

         }

         return $final_answer;
   }

     public function adjudicarHotsale($idResidenciaSemana,$idUser){
        $answer = $this->queryList("UPDATE hotsale SET idUsuario=:idUser ,activa= 0,borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana, ':idUser'=>$idUser));
        return true;




      }

      public function borrarSemanaHotsale($idResidenciaSemana){
         $answer = $this->queryList("UPDATE hotsale SET activa= 0,borrada = 1 WHERE idResidenciaSemana = :idResidenciaSemana",array(':idResidenciaSemana'=> $idResidenciaSemana));
        return true;




      }



}