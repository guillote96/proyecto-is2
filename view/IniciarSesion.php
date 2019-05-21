<?php


class IniciarSesion extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('iniciarsesion.html',$datos);
        
        
    }
    
}
