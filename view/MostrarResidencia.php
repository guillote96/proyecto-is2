<?php


class MostrarResidencia extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('mostrarPublicacionResidencia.html',$datos);
        
        
    }

    
}
