<?php
require_once('controller/Controller.php');

class AdministradorController extends Controller {

	private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }
    


   public function adminLogin(){
     // TODO: Validaciones del lado del servidor
    if(empty($_POST['username-input-admin']) || empty($_POST['password-input-admin'])){
              $this->vistaIniciarSesion(array('mensaje' => "Hay Campos Vacios"));
             return false;
        }

     if($_POST['username-input-admin'] == "admin" && $_POST['password-input-admin'] == 1234){
       $this->alta_sesion($_POST['username-input-admin'], 1, "administrador"); // el id es ficticio para esta entrega
       $this->vistaAdminPanel($_POST['username-input-admin']);
     }else{
       $this->vistaIniciarSesion(array('mensaje' => "Usuario o contraseña incorrecta"));
       return false;
     }
   }


  public function vistaAdminPanel($user){
    $view = new AdminPanel();
    $listaresidencia=PDOResidencia::getInstance()->listarTodas();
    if(empty($user))
      $view->show(array('user' => null,'listaresidencia'=> $listaresidencia));
    else
      $view->show(array('user' => $user,'listaresidencia'=> $listaresidencia));
  }


}