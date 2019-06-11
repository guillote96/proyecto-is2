<?php

class VerPerfil extends TwigView {

  public function show($datos) {

    echo self::getTwig()->render('verperfil.html', $datos);


  }


}