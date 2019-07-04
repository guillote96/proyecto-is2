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

  public function buscarSemanas(){


      $directasActivas= PDODirecta::getInstance()->listarTodasDirectas();
      $directasFinalizadas= PDODirecta::getInstance()->listarDirectasFinalizadas();
      $directas=array('activas'=>$directasActivas,'finalizadas' => $directasFinalizadas);


      $subastasSinMonto=PDOSubasta::getInstance()->listarSubastaInactivasSinMonto();
      $subastasActivas=PDOSubasta::getInstance()->listarTodasSubasta ();
      $subastasFinalizadas=PDOSubasta::getInstance()->listarSubastasFinalizadas();

      $subastas=array('activas'=>$subastasActivas,'inactivas' => $subastasSinMonto,'finalizadas'=>$subastasFinalizadas);

     $posiblesHotsale=PDOHotsale::getInstance()->listarTodosHotsaleDeshabilitado();
     $hotsaleActivos= PDOHotsale::getInstance()->listarTodosHotsale();
     $hotsaleFinalizados= PDOHotsale::getInstance()->listarHotsaleFinalizados();

      $hotsale=array('hotsales' => $posiblesHotsale,'hotsalesactivos'=>$hotsaleActivos,'hotsalesfinalizados' => $hotsaleFinalizados);


     $view= new Semana();
    if(($subastas != false) || ($directas != false) || ($hotsale != false)){ 
    $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => null,'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
      }
    else{
      $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => "No hay Resultados",'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
    }


  }


}