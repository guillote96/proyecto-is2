<?php


class EstadoDirecta extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('directaestado.html', $datos);
        
        
    }

    
}
