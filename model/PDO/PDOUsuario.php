<?php


class PDOUsuario extends PDORepository {

  private static $instance;

  public static function getInstance()
  {

    if (!isset(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function __construct()
  {

  }


   public function traerUsuario($idUsuario){

        $answer = $this->queryList("SELECT * FROM usuario WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

        $final_answer = [];
        foreach ($answer as &$element) {
            return new Usuario($element['idUsuario'],$element['email'],$element['password'], $element['nombre'],$element['apellido'],$element['tarjeta'],(int)$element['creditos'],$element['fecha_nac'],$element['fecha_reg'],$element['borrada']);

        }

   }

   public function decrementarCreditos($idUsuario){
      $answer = $this->queryList("UPDATE usuario SET creditos = creditos - 1  WHERE idUsuario=:idUsuario",array(':idUsuario'=> $idUsuario));

     }



}