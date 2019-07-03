<?php

class PDOSemana extends PDORepository {

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


   public function semanasNoIncluidas ($idResidencia){
    //Para determinada residencia, busca aquellas semanas que no esten en la tabla de residencia_semana y devuelve las semanas no incluidas.
     $answer = $this->queryList("SELECT * FROM semana sem WHERE sem.idSemana NOT IN (SELECT rs.idSemana FROM residencia_semana rs WHERE rs.idResidencia=:idResidencia)",array(':idResidencia' => $idResidencia));

     $final_answer = [];
      foreach ($answer as &$element) {
         $final_answer[] = new Sem ($element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["idAdministrador"],$element["fecha_creacion"]);
      }

    return $final_answer;

   }

   public function insertarSemana($fecha_inicio,$fecha_fin,$fecha_creacion){
  $answer = $this->queryList("INSERT INTO semana (fecha_inicio, fecha_fin, fecha_creacion,idAdministrador,borrada) VALUES(:fecha_inicio,:fecha_fin,:fecha_creacion,:idAdministrador,:borrada);",array(':fecha_inicio' => $fecha_inicio,':fecha_fin'=> $fecha_fin,':fecha_creacion'=> $fecha_creacion,':idAdministrador'=> $_SESSION['id'],':borrada'=>0));

   }

   public function  buscarSemana($fecha_inicio,$fecha_fin){
    $answer = $this->queryList("SELECT * FROM semana sem WHERE sem.fecha_inicio=:fecha_inicio AND sem.fecha_fin=:fecha_fin ORDER BY sem.idSemana DESC LIMIT 1",array(':fecha_inicio' => $fecha_inicio,':fecha_fin'=>$fecha_fin));
    $final_answer = [];
      foreach ($answer as &$element) {
         $final_answer[] = new Sem ($element["idSemana"],$element["fecha_inicio"],$element["fecha_fin"],$element["idAdministrador"],$element["fecha_creacion"]);
      }

      return $final_answer;

   }


}