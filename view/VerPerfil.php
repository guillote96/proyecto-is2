<?php

class VerPerfil extends TwigView {

  public function show($datos) {

    echo self::getTwig()->render('verperfil.html', $datos);


  }


  public function showHistorial($datos) {

    echo self::getTwig()->render('historialuser.html', $datos);


  }


}