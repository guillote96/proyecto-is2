<?php


class AdminPanel extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('admin_panel.html', $datos);
        
        
    }


    public function sistemaPanel(){

    	echo self::getTwig()->render('panel_sistema.html');
    }

    
}
