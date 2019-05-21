<?php


class CargarResidencia extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('cargaresidencia.html',$datos);
        
        
    }


    public function editarResidencia($datos){
       
       echo self::getTwig()->render('editaresidencia.html',$datos);

     }

    
}
