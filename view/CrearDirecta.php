<?php


class CrearDirecta extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('creardirecta.html', $datos);
        
        
    }

    
}
