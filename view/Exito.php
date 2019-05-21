<?php


class Exito extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('exito.html',$datos);
        
        
    }

    
}