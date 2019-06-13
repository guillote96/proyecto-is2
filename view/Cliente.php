<?php


class Cliente extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('listarCliente.html', $datos);
        
        
    }

    
}
