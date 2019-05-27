<?php


class CrearSubasta extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('crearsubasta.html', $datos);
        
        
    }

    
}
