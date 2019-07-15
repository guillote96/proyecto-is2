<?php


class Admin extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('listarAdmin.html', $datos);
        
        
    }

    public function showRegistrar($datos) {
        
        echo self::getTwig()->render('registrarAdmin.html', $datos);
        
        
    }

    
}