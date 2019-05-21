<?php


class EstadoSubasta extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('subastaestado.html', $datos);
        
        
    }

    
}
