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
  
  if($this->verificarDatosUsuario()){

    $this->altaRegistro();
      return true;
    }
  else{
     return false;
    }
  }


  public function verificarDatosUsuario(){
      
    if(empty($_POST['nombre-input-signup']) || empty($_POST['apellido-input-signup'])|| empty($_POST['email-input-signup'])|| empty($_POST['password-input-signup'])|| empty($_POST['tarjeta-input-signup']) || empty($_POST['fechanacimiento-input-signup'])){

       $this->vistaIniciarSesion(array('mensaje' => "Hay Campos Vacios"));

       return false;
     }

     else {
        $fechanacimiento = new DateTime($_POST['fechanacimiento-input-signup']);
        $fechaactual = new DateTime("now");
        $diff = $fechanacimiento->diff($fechaactual);
                            
        //  echo $diff->y .' años ';
        if($diff->y<18){
          $this->vistaIniciarSesion(array('mensaje' => "Debe ser mayor a 18 años de edad"));

           return false;

          }
         //si no existe la sesion pasa se fija si el mail del registro no esta en la base, si el mail que ingrese en el editar perfil es distinto al que tengo en SESSION entonces se fija en la base si existe el nuevo email 
        if(!isset($_SESSION['usuario']) || $_SESSION['usuario']!=$_POST['email-input-signup']){
            if(PDOUsuario::getInstance()->existeEmail($_POST['email-input-signup'])){

              $this->vistaIniciarSesion(array('mensaje' => "El email ya se encuentra registrado por otro usuario"));

              return false;
          }
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


  public function verPerfil(){
        
       
        $unUsuario=PDOUsuario::getInstance()->traerUsuario($_SESSION['id']); 
        $esPremium=PDOUsuario::getInstance()->esPremium($_SESSION['id']);
        $view = new VerPerfil();
        $view->show(array('esPremium' => $esPremium,'datos' => $unUsuario));
  }


  public function detallesUsuario($idUsuario){

        $unUsuario=PDOUsuario::getInstance()->traerUsuario($idUsuario);  
        $view = new VerPerfil();
        $view->show(array('user' => $idUsuario,'datos' => $unUsuario, "tipousuario"=> $_SESSION['tipo']));
  }


public function editarPerfil(){
        
        $unUsuario=PDOUsuario::getInstance()->traerUsuario($_SESSION['id']); 
        $view = new EditarPerfil();
        $view->show(array('user' => $_SESSION['id'],'datos' => $unUsuario));
       

  }

  public function procesarEdicionPerfil(){

         if ($this->verificarDatosUsuario()){
           PDOUsuario::getInstance()->actualizarUsuario($_SESSION['id']);
         //pregunta si el mail del editar registro es igual al que esta en SESSION, si lo cambie hay que cerrar sesion por seguridad.
         if($_SESSION['usuario']==$_POST['email-input-signup']){

               $this->vistaExito(array('id' => $_SESSION['id'], 'mensaje' => 'Los datos del usuario fueron actualizados con exito! ', 'exito' => true,'tipousuario'=>$_SESSION['tipo'],'user'=> $_SESSION['usuario']));
               return true;
            }
          else{
            $this->cerrarSesion();
            }
          }
  }

  public function listarClientes(){
    $usuarios= PDOUsuario::getInstance()->listarUsuarios();
    $view=new Cliente();

    $view->show(array('clientes'=>$usuarios,'user'=> $_SESSION['usuario'],'tipousuario'=> $_SESSION['tipo']));


  }

  public function buscarCliente(){
    $usuarios= PDOUsuario::getInstance()->buscarUsuario();
    $view=new Cliente();
    $view->show(array('clientes'=>$usuarios,'user'=> $_SESSION['usuario'],'tipousuario'=> $_SESSION['tipo']));
  }



  public function quieroCambiarTipoUsuario(){

    if(!$this->seEnvioSolicitud($_SESSION['id'])){
      $this->permitirEnvio();
    }
    else{
      $this->vistaExito(array('id' => $_SESSION['id'], 'mensaje' => 'Usted ya envio una solicitud, espere a ser contactado ', 'exito' => true));
    }
  }

  public function permitirEnvio(){

    if(PDOUsuario::getInstance()->esPremium($_SESSION['id'])){
      PDOUsuario::getInstance()->envioPasarAEstandar($_SESSION['id']);
      $this->vistaExito(array('id' => $_SESSION['id'], 'mensaje' => 'La solicitud para pasar a Usuario Estandar fue enviada con exito!
      Sera informado a la brevedad ', 'exito' => true));
    }
    else{ //Si es Estandar
      PDOUsuario::getInstance()->envioPasarAPremium($_SESSION['id']);
      $this->vistaExito(array('id' => $_SESSION['id'], 'mensaje' => 'La solicitud para pasar a Usuario Premium fue enviada con exito 
        Sera informado a la brevedad! ', 'exito' => true));
    }
  }
  



    public function seEnvioSolicitud($idUsuario){
      if(PDOUsuario::getInstance()->existeSolicitud($idUsuario)) {
        return true;
      }
      else{
        return false;
      }   
    }

   
  public function cambiarRol($idUsuario){
      PDOUsuario::getInstance()->actualizarSolicitud($idUsuario);

      if(PDOUsuario::getInstance()->esPremium($idUsuario)){
        
        PDOUsuario::getInstance()->pasarAEstandar($idUsuario);
        
      }
      else{

        if(PDOUsuario::getInstance()->yaFuePremium($idUsuario)){

           PDOUsuario::getInstance()->pasarAPremium($idUsuario);
           
        }
        else{
          PDOUsuario::getInstance()->insertarNuevoPremium($idUsuario);
        }
      }  

      $this-> listarClientes();
    }

    

 










}
