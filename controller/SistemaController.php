<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/PHPMailer/src/Exception.php';
require './vendor/PHPMailer/src/PHPMailer.php';
require './vendor/PHPMailer/src/SMTP.php';

class SistemaController extends Controller {

	private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }


    public function vistaPanel(){
    	$view=new AdminPanel();
    	$view->sistemaPanel();

    }

  public function solicitarPassword($emailReceptor){

    if(PDOUsuario::getInstance()->existeEmail($emailReceptor)){
       $token = bin2hex(random_bytes(8)); 
       PDOUsuario::getInstance()->actualizarToken($emailReceptor,$token);
       $mensaje="Si usted solicito un cambio de contraseña, por favor dirijase al siguiente enlace: http://localhost/proyecto-is2/index.php?action=cambiarPassword&email=$emailReceptor&token=$token";
        $this->enviarEmail($emailReceptor,$mensaje,'Solicitud de cambio de contraseña');
        
     }

     $this->vistaExito(array('mensaje' => "Revise su bandeja de correo..El email fue enviado"));


  }


  public function enviarEmail($emailReceptor,$mensaje,$subject){
     $mail = new PHPMailer;
     $mail->isSMTP();
     $mail->SMTPSecure = 'tls';
     $mail->SMTPAuth = true;
     $mail->Host = 'smtp.gmail.com';
     $mail->Port = 587;

     $mail->Username = 'amca83074@gmail.com';
     $mail->Password = '@123qwe123qwe';
     $mail->setFrom('amca83074@gmail.com');
     $mail->addAddress($emailReceptor);
     $mail->Subject = $subject;
     $mail->Body = $mensaje;
     //send the message, check for errors
     if (!$mail->send()) {
       return false;
     } else {
        return true;
     }

    }


    public function cambiarPassword(){
        //verificar token e email coincidan
        $email=$_GET['email'];
        $token=$_GET['token'];
        $datos=PDOUsuario::getInstance()->traerUsuarioYtoken($email);
       
       if($email== $datos['email'] && $token== $datos['token']){
         $view=new CambiarPassword();
         $view->show(array('email'=>$email));
       }else{

         $this->vistaExito(array('mensaje' => "Ups! Sucedio un error"));
       }
    }

    public function procesar_cambioPassword(){

        if($_POST['password']==$_POST['password2']){

            PDOUsuario::getInstance()->actualizarPassword($_GET['email'],$_POST['password']);
           $this->vistaExito(array('mensaje' => "¡El password fue cambiado exitosamente!"));

        }else{

            $view=new CambiarPassword();
            $view->show(array('email'=>$_GET['email'],'mensaje'=>'Los password ingresados no coinciden..Intente de nuevo'));
        }


    }








}
    
