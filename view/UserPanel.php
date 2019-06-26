<?php

class UserPanel extends TwigView {

  public function show($datos) {

    echo self::getTwig()->render('buscarSemanas.html', $datos);


  }


}