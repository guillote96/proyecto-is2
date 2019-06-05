<?php


class IngresoMonto extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('ingresomonto.html', $datos);
        
        
    }

    
}
