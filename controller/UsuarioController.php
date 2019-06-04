<?php
require_once('controller/Controller.php');

class UsuarioController extends Controller {
    
    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }
    
  public function userLogin(){
    // TODO: Validaciones del lado del servidor
     if(empty($_POST['email-input-login']) || empty($_POST['password-input-login'])){
       $this->vistaIniciarSesion(array('mensaje' => "Hay Campos Vacios"));
       return false;
     }

    $usuario=PDOUsuario::getInstance()->traerUsuarioPorEmail($_POST['email-input-login']);
     if($usuario == false){
      //no existe usuario
       $this->vistaIniciarSesion(array('mensaje' => "Email o contraseña incorrecta"));
       return false;
     }


    if($_POST['email-input-login'] == $usuario->getEmail() && $_POST['password-input-login'] == $usuario->getPassword()){

      if(PDOUsuario::getInstance()->esPremium($usuario->getIdUsuario())) 
        $this->alta_sesion($_POST['email-input-login'], $usuario->getIdUsuario(), "premium");
      else
         $this->alta_sesion($_POST['email-input-login'], $usuario->getIdUsuario(), "usuario");

      $this->vistaUserPanel($_POST['email-input-login']);
    }else{
      $this->vistaIniciarSesion(array('mensaje' => "Email o contraseña incorrecta"));
      return false;
    }
  }



public function userSignup(){
  
  if($this->verificarRegistro()){

    $this->altaRegistro();
      return true;
    }
  else{
     return false;
    }
  }


  public function verificarRegistro(){
      
    if(empty($_POST['nombre-input-signup']) || empty($_POST['apellido-input-signup'])|| empty($_POST['email-input-signup'])|| empty($_POST['password-input-signup'])|| empty($_POST['tarjeta-input-signup']) || empty($_POST['fechanacimiento-input-signup'])){

       $this->vistaIniciarSesion(array('mensaje' => "Hay Campos Vacios"));

       return false;
     }

     else {
      $fechanacimiento = new DateTime($_POST['fechanacimiento-input-signup']);
      $fecharegistro = new DateTime("now");
      $diff = $fechanacimiento->diff($fecharegistro);
                            
    //  echo $diff->y .' años ';
        if($diff->y<18){
          $this->vistaIniciarSesion(array('mensaje' => "Debe ser mayor a 18 años de edad"));

           return false;

          }
      }   

     return true;
  }

  public function altaRegistro(){
      // Inserta el usuario
       PDOUsuario::getInstance()->insertarUsuario();
        $this->vistaExito(array('mensaje' =>"¡¡¡El usuario fue cargado exitosamente!!!"));
        return true;
       
    }

  

  public function vistaUserPanel($user){
    $view = new UserPanel();
    $listaresidencia=PDOResidencia::getInstance()->listarTodas();
    if(empty($user))
      $view->show(array('user' => null,'listaresidencia'=> $listaresidencia));
    else
      $view->show(array('user' => $user,'listaresidencia'=> $listaresidencia));
  }


}
