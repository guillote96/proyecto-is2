<?php


class EstadoHotsale extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('hotsaleestado.html', $datos);
        
        
    }

    
}
