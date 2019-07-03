<?php


class CambiarPassword extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('cambiarPassword.html',$datos);
        
        
    }
    
}
