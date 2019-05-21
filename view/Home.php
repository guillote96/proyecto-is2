<?php


class Home extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('home_v2.html',$datos);
        
        
    }

    
}
