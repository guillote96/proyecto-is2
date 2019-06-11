<?php

class EditarPerfil extends TwigView {

  public function show($datos) {
  	
    echo self::getTwig()->render('editarperfil.html', $datos);


  }


}