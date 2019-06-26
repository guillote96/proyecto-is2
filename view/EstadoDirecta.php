<?php


class EstadoDirecta extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('directaestado.html', $datos);
        
        
    }

    public function panelDirectas($datos){
    	echo self::getTwig()->render('panel_directas.html', $datos);


    }

    
}
