<?php


class Semana extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('versemana.html',$datos);
        
        
    }

    
}