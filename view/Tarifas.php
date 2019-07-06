<?php


class Tarifas extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('tarifas.html', $datos);
        
        
    }

   
    
}