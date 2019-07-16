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
     $admin=PDOAdmin::getInstance()->traerAdminPorUsername($_POST['username-input-admin']);
     
     if($admin == false){
      //no existe usuario
       $this->vistaIniciarSesion(array('mensaje' => "Email o contraseña incorrecta"));
       return false;
     }

     if($_POST['username-input-admin'] == $admin->getUsername() && $_POST['password-input-admin'] == $admin->getPassword()){
       $this->alta_sesion($_POST['username-input-admin'],$admin->getIdAdmin(), "administrador"); // el id es ficticio para esta entrega
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
    if(($subastas['activas'] != false) || ($subastas['inactivas'] != false)|| ($subastas['finalizadas'] != false)   || ($directas['activas'] != false) || ($directas['finalizadas'] != false) || ($hotsale['hotsales'] != false) || ($hotsale['hotsalesactivos'] != false) ||($hotsale['hotsalesfinalizados'] != false)){ 
    $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => null,'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
      }
    else{
      $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => "No hay Resultados",'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"]));
    }


  }



  public function buscar_semanas(){


      $directasActivas= PDODirecta::getInstance()-> buscarDirectasAdminActivas();
      $directasFinalizadas= PDODirecta::getInstance()-> buscarDirectasAdminFinalizadas();
      $directas=array('activas'=>$directasActivas,'finalizadas' => $directasFinalizadas);


      $subastasSinMonto=PDOSubasta::getInstance()->buscarSubastaAdminInactivasSinMonto();
      $subastasActivas=PDOSubasta::getInstance()->buscarSubastaAdminActivas();
      $subastasFinalizadas=PDOSubasta::getInstance()->buscarSubastaAdminFinalizadas();

      $subastas=array('activas'=>$subastasActivas,'inactivas' => $subastasSinMonto,'finalizadas'=>$subastasFinalizadas);

     $posiblesHotsale=PDOHotsale::getInstance()->buscarHotsalesAdminDeshabilitados();
     $hotsaleActivos= PDOHotsale::getInstance()->buscarHotsalesAdminActivas();
     $hotsaleFinalizados= PDOHotsale::getInstance()->buscarHotsalesAdminFinalizados();

      $hotsale=array('hotsales' => $posiblesHotsale,'hotsalesactivos'=>$hotsaleActivos,'hotsalesfinalizados' => $hotsaleFinalizados);


     $view= new Semana();
    if(($subastas['activas'] != false) || ($subastas['inactivas'] != false)|| ($subastas['finalizadas'] != false)   || ($directas['activas'] != false) || ($directas['finalizadas'] != false) || ($hotsale['hotsales'] != false) || ($hotsale['hotsalesactivos'] != false) ||($hotsale['hotsalesfinalizados'] != false)){
    $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,'hotsales'=>$hotsale),'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"], 'mensaje' => null));
      }
    else{
      $view->buscarSemanaAdmin(array('datos' => array("subastas"=>$subastas,"directas"=>$directas),'tipo'=> $_SESSION['tipo'],'idUser' => $_SESSION["id"],'mensaje' => "No hay Resultados"));
    }

   }



   public function listarAdmins(){
    $administradores= PDOAdmin::getInstance()->listarAdministradores();

    $view=new Admin();

    $view->show(array('administradores'=>$administradores,'user'=> $_SESSION['usuario'],'tipousuario'=> $_SESSION['tipo']));


  }


public function adminSignup(){
  
  if($this->verificarDatosAdmin()){

    $this->altaRegistroAdmin();
      return true;
    }
  else{
     return false;
    }
  }


  public function verificarDatosAdmin(){
      
    if(empty($_POST['nombre-input-signup']) || empty($_POST['apellido-input-signup'])|| empty($_POST['username-input-signup'])|| empty($_POST['password-input-signup'])|| empty($_POST['dni-input-signup']) ){

        
       $this->registrarAdmin(array('hayError'=> true,'mensaje' => "Hay Campos Vacios"));

       return false;
     }

     else {
        
        
         //si no existe la sesion pasa se fija si el mail del registro no esta en la base, si el mail que ingrese en el editar perfil es distinto al que tengo en SESSION entonces se fija en la base si existe el nuevo email 
        
            if(PDOAdmin::getInstance()->existeUsername($_POST['username-input-signup'])){

              $this->registrarAdmin(array('hayError'=> true,'mensaje' => "El username ya se encuentra registrado por otro administrador. Debe ingresar otro username "));

              return false;
          }
        

        
      }   

     return true;
  }


  

  public function altaRegistroAdmin(){
      // Inserta el usuario
       PDOAdmin::getInstance()->insertarAdmin();
        $this->vistaExito(array('mensaje' =>"¡¡¡El administrador fue cargado exitosamente!!!"));
        return true;
       
    }



  public function registrarAdmin($hayVentana){
    $view = new Admin();
    
    $view->showRegistrar(array('idUser' => $_SESSION['id'],'user' => $_SESSION['usuario'], "tipousuario"=> $_SESSION['tipo'],'hayVentana' => $hayVentana));
  
  }
  
public function desactivarCuentaAdmin($idAdministrador){
  PDOAdmin::getInstance()->desactivarCuenta($idAdministrador);

  if(!PDOAdmin::getInstance()->hayMasAdministradores()){
      PDOAdmin::getInstance()->reActivarCuenta($idAdministrador);
      $this->vistaExito(array('mensaje' =>"Usted es el ultimo administrador, no puede darse de baja hasta que registre un nuevo administrador"));
  }
    
  else{
        
        if($_SESSION['id']==$idAdministrador){

            $this->cerrarSesion();
        }
        else{
            
            $this->listarAdmins();
        }    
  }      
}



}