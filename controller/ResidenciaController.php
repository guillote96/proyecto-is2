<?php

require_once('controller/Controller.php');

class ResidenciaController extends Controller {

	private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    private function __construct() {
        
    }

    public function cargarResidencia($datos){
        if(empty($_SESSION['usuario']) && empty($_SESSION['tipo'])){
            return false;
        }
        if($_SESSION['tipo'] == "administrador"){
    	     $view = new CargarResidencia();
    	     $view->show(array('user' => $_SESSION['usuario'], 'datos' => $datos));
        }

    }

    public function procesarAltaResidencia(){

      if ($this->verificarDatosResidencia()){
          $this->altaResidencia();
          return true;
      }

      $this->cargarResidencia(array('mensaje' => "Ups hubo un error. Intente de nuevo y llene TODOS los campos"));
      return false;

    }

    public function verificarDatosResidencia(){

        if(!isset($_SESSION['tipo']) ||  $_SESSION['tipo'] != "administrador" ){
            // mensaje de error (no tiene autorizacion)
            return false;

        }
        if(empty($_POST['titulo']) || empty($_POST['idProvincia']) || empty($_POST['idPartido']) || empty($_POST['idLocalidad']) || empty($_POST['direccion'] ) ||  empty($_POST['descripcion'])){
           
            return false;
        }

        return true;

    }


    public function altaResidencia(){
      // Inserta la residencia
       PDOResidencia::getInstance()->insertarResidencia();
        $this->vistaExito(array('mensaje' =>"¡¡¡La residencia fue cargada exitosamente!!!", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        return true;
       
    }

    public function mostrarResidencia($datos){
        $view= new MostrarResidencia();
        if(!isset($_SESSION['usuario']) ||  empty($_SESSION['usuario']))
           $view->show(array('user' => null,'tipousuario' => null,'residencia' => PDOResidencia::getInstance()->traerResidencia($datos['id']), 'residenciasemanasubastas'=> PDOResidenciaSemana::getInstance()->traerResidenciaSemanasSubastas($datos['id'])));

        else
          //PDOResidenciaSemana::getInstance()->traerResidenciaSemanasSubastas($datos['id']),'datos' => $datos)
        $view->show(array('user' => $_SESSION['usuario'],'tipousuario' => $_SESSION['tipo'], 'residencia' => PDOResidencia::getInstance()->traerResidencia($datos['id']), 'residenciasemanasubastas' => AuctionsController::getInstance()->listadoSubastasHabilitadas($datos['id']),'datos' => $datos ));
    }

    public function vistaSemana($datos){

        $view = new Semana();
        $view->show($datos);

    }


    public function verSemana($idRS){
        //idRS = identificador de residencia semana... Esto es solo para las subastas
        $subasta = PDOSubasta::getInstance()->subastaInfo($idRS);

        if(empty($subasta) ||  empty($_SESSION['usuario'])){
            $this->vistaExito(array('mensaje' =>"Debe iniciar Sesion...", 'user' =>null,'tipousuario'=>$_SESSION['tipo']));
            return false;
        }

         //indexo la subasta (recordar que me manda un arreglo y siempre va a ser un solo elemento)

         $datos= array('user' => $_SESSION['usuario'],'tipousuario' => $_SESSION['tipo'],'base' => $subasta[0]->getBase(),'idSubasta' => $subasta[0]->getIdSubasta(), 'puja' => PDOSubasta::getInstance()->pujaMaximaSubasta($subasta[0]->getIdSubasta()));

         if(PDOSubasta::getInstance()->idUsuarioPujaMaximaSubasta($subasta[0]->getIdSubasta()) == $_SESSION['id'])
             $datos['boton']=false;
         else
            $datos['boton']=true;


         $this->vistaSemana($datos);

     }


     public function verificarDatosPuja($idSubasta, $puja){
        //falta mandar vista de errores y de exito

        $usuario=PDOUsuario::getInstance()->traerUsuario($_SESSION['id']);

        $creditos=1; // esto existe por que no se hacen consultas de primera movida (despues se saca)
        if($_SESSION['tipo'] == "administrador"){
               $this->vistaExito(array('mensaje' =>"No tiene permisos para hacer esta accion", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
               return false;

        }
        if (PDOSubasta::getInstance()->esMayorPuja($idSubasta, $puja) && $usuario->getCreditos() > 0) {

          PDOSubasta::getInstance()->insertarParticipanteSubasta($_SESSION['id'], $idSubasta, $puja);
          $this->vistaExito(array('mensaje' =>"¡¡¡La Puja fue registrada!!!", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
            return true;
        }
        if($usuario->getCreditos() == 0){

          $this->vistaExito(array('mensaje' =>"Creditos Insuficientes", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
        
        }
       return false;
      
     }

   public function editarResidencia($idResidencia){
       // se puede editar una residencia si NO hay partipantes en las semanas correspondiente a la misma
        if($_SESSION['tipo'] != "administrador"){
            $this->vistaExito(array('mensaje' =>"No tiene permisos para hacer esta accion", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
               return false;

        }
    if(!PDOResidencia::getInstance()->existenParticipantes($idResidencia)){
       $residencia= PDOResidencia::getInstance()->traerResidencia($idResidencia);
       $view= new CargarResidencia();
       $view->editarResidencia(array('user' => $_SESSION['usuario'],'datos' => $residencia[0]));
       return true;
     }

     $this->vistaExito(array('mensaje' =>"No puede editarse.Ya existen Participantes", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
     return false;

   }

   public function procesarEdicionResidencia($idResidencia){

    if ($this->verificarDatosResidencia()){
         PDOResidencia::getInstance()->actualizarResidencia($idResidencia);
         $this->mostrarResidencia(array('id' => $idResidencia, 'mensaje' => 'La residencia fue actualizada con exito', 'exito' => true));
         return true;
    }

    $this->mostrarResidencia(array('id' => $idResidencia, 'mensaje' => 'La publicacion no fue editada', 'exito' => false));
      return false;

   }


   public function cancelarEdicion($idResidencia){

      $this->mostrarResidencia(array('id' => $idResidencia, 'mensaje' => '¡¡Edicion Cancelada!!', 'exito' => true));
       return true;
   }



   public function eliminarResidencia($idResidencia){
     
      if(empty($_SESSION['usuario']) || empty($_SESSION['tipo']) || $_SESSION['tipo'] != "administrador"){

            $this->vistaExito(array('mensaje' =>"No tiene permisos para hacer esta accion", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
               return false;
        }

      else{
    
   //     if($_SESSION['tipo'] == "administrador"){
    //      if (!PDOResidencia::getInstance()->existenParticipantes($idResidencia)){      

              PDOResidencia::getInstance()->borrarResidencia($idResidencia);

              $this->vistaExito(array('mensaje' =>"La residencia se elimino satisfactoriamente", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));  
     /* Por si volvemos a usar el borrado fisico         
            }
           else{
              $this->vistaExito(array('mensaje' =>"La Residencia no pudo ser eliminada, debido a que se encuentra reservada ", 'user' => $_SESSION['usuario'],'tipousuario'=>$_SESSION['tipo']));
               return false;
               }
      */    
       }
     }

       

   public function crearSubasta($idResidencia){
      AuctionsController::getInstance()->crearSubasta($idResidencia);
   }



  public function sincronizador(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){

       ResidenciaSemanaController::getInstance()->sincronizador($residencia->getIdResidencia());

      }

      AdministradorController::getInstance()->vistaAdminPanel($_SESSION['usuario']);


   }


  public function sincronizadorDirectas(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){
       DirectaController::getInstance()->sincronizador($residencia->getIdResidencia());

      }

      SistemaController::getInstance()->vistaPanel();

   }


     public function sincronizadorSubastas(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
      foreach ($residencias as $key => $residencia){
       AuctionsController::getInstance()->sincronizador($residencia->getIdResidencia());

      }
      SistemaController::getInstance()->vistaPanel();


   }


     public function sincronizadorHotsales(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
      foreach ($residencias as $key => $residencia){
       HotsaleController::getInstance()->sincronizador($residencia->getIdResidencia());

      }
      SistemaController::getInstance()->vistaPanel();


   }


     public function sincronizadorDirectas2021(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){
       DirectaController::getInstance()->sincronizador2021($residencia->getIdResidencia());

      }

      SistemaController::getInstance()->vistaPanel();

   }

        public function sincronizadorSubastas2021(){
      // Sincroniza todas las semanas para cada residencia.
     $residencias= PDOResidencia::getInstance()->listarTodas();
     foreach ($residencias as $key => $residencia){
       AuctionsController::getInstance()->sincronizador2021($residencia->getIdResidencia());

      }

      SistemaController::getInstance()->vistaPanel();

   }









   public function buscarResidencia(){

     $residencias=PDOResidencia::getInstance()->buscarResidencia();
     if($residencias != false){
       if (sizeof($residencias)>0 ) {
         $this->adminPanel(array('residencias' => $residencias, 'mensaje' => null));
         return true;
        }     
     }
     $this->adminPanel(array('residencias' => $residencias, 'mensaje' => 'No hay Resultados'));
     return false;



   }

   public function buscar_semanas(){
     $subastas=PDOSubasta::getInstance()->listarTodasSubasta();
     $directas=PDODirecta::getInstance()->listarTodasDirectas();
     $hotsale= PDOHotsale::getInstance()->listarTodosHotsale();
     $view= new Semana();
    if(($subastas != false) || ($directas != false) || ($hotsale != false)){ 
    $view->buscarSemana(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => null,'tipo'=> $_SESSION['tipo'],'user' => $_SESSION['usuario'],'idUser' => $_SESSION["id"]));
  }
    else{
      $view->buscarSemana(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=>$hotsale), 'mensaje' => "No hay Resultados",'tipo'=> $_SESSION['tipo'],'user' => $_SESSION['usuario'],'idUser' => $_SESSION["id"]));
    }


   }

   public function buscarSemanas(){
    
     $subastas=PDOSubasta::getInstance()->buscarSubasta();
     $directas=PDODirecta::getInstance()->buscarDirectas();
     $hotsales=PDOHotsale::getInstance()->buscarHotsales();
     $view= new Semana();


    
    if(($subastas != false) || ($directas != false) || ($hotsales != false)){ 

     $view->buscarSemana(array('datos' => array("subastas"=>$subastas,"directas"=>$directas,"hotsales"=> $hotsales), 'mensaje' => null,'tipo'=> $_SESSION['tipo']));
       return true;   
     }
      $view->buscarSemana(array('datos' => null, 'mensaje' => 'No hay Resultados'));
      return false;


   }
public function crearSemanaDirectaParaTodasLasResidencias(){

  $residencias= PDOResidencia::getInstance()->listarTodas();
  foreach ($residencias as $key => $residencia){
      DirectaController::getInstance()->crearSemanaDirectaPanelSistema($residencia->getIdResidencia());

   }

     SistemaController::getInstance()->vistaPanel();

    
}





}
    