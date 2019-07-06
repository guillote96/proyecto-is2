<?php


class Semana extends TwigView {
    
    public function show($datos) {
        
        echo self::getTwig()->render('versemana.html',$datos);
        
        
    }

    public function buscarSemana($datos) {
        
        echo self::getTwig()->render('buscarSemanas.html',$datos);
        
        
    }

    public function buscarSemanaAdmin($datos){
    	 echo self::getTwig()->render('buscarSemanasAdmin.html',$datos);


    }

    public function editarSemana($datos){
        echo self::getTwig()->render('editarSemana.html',$datos);


    }


    
}