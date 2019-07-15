<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');

/* CONTROLLER */
require_once('controller/UsuarioController.php');
require_once('controller/AuctionsController.php');
require_once('controller/ResidenciaController.php');
require_once('controller/AdministradorController.php');
require_once('controller/AuctionsController.php');
require_once('controller/DirectaController.php');
require_once('controller/HotsaleController.php');
require_once('controller/ResidenciaSemanaController.php');
require_once('controller/TarifasController.php');
require_once('controller/SistemaController.php');



/* VIEW */
require_once('view/TwigView.php');
require_once('view/Home.php');
require_once('view/AdminPanel.php');
require_once('view/UserPanel.php');
require_once('view/IniciarSesion.php');
require_once('view/Exito.php');
require_once('view/Semana.php');
require_once('view/CargarResidencia.php');
require_once('view/MostrarResidencia.php');
require_once('view/EstadoSubasta.php');
require_once('view/AuctionsView.php');
require_once('view/CrearSubasta.php');
require_once('view/CrearDirecta.php');
require_once('view/EstadoDirecta.php');
require_once('view/EstadoHotsale.php');
require_once('view/IngresoMonto.php');
require_once('view/VerPerfil.php');
require_once('view/EditarPerfil.php');
require_once('view/Cliente.php');
require_once('view/Tarifas.php');
require_once('view/CambiarPassword.php');
require_once('view/Admin.php');

/* PDO */
require_once('model/PDO/PDORepository.php');
require_once('model/PDO/PDOResidencia.php');
require_once('model/PDO/PDOUsuario.php');
require_once('model/PDO/PDOSubasta.php');
require_once('model/PDO/PDOResidenciaSemana.php');
require_once('model/PDO/PDOHotsale.php'); 
require_once('model/PDO/PDOSemana.php');
require_once('model/PDO/PDODirecta.php');
require_once('model/PDO/PDOHotsale.php');
require_once('model/PDO/PDOTarifa.php');
require_once('model/PDO/PDOAdmin.php');

/* MODEL */

require_once('model/Subasta.php');
require_once('model/Hotsale.php');
require_once('model/Usuario.php');
require_once('model/Directa.php');
require_once('model/ResidenciaSemana.php');
require_once('model/Residencia.php');
require_once('model/Sem.php');
require_once('model/AuctionDetail.php');
require_once('model/Administrador.php');


if(isset($_GET["action"]) && $_GET["action"] == 'iniciarsesion'){
     Controller::getInstance()->vistaIniciarSesion(null);   
}

else if(isset($_GET["action"]) && $_GET["action"] == 'verificarDatosUsuario'){
     UsuarioController::getInstance()->verificarDatos();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'verificarDatosAdministrador'){
     AdministradorController::getInstance()->verificarDatos();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cerrarSesion'){
     Controller::getInstance()->cerrarSesion();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cargarResidencia'){
     ResidenciaController::getInstance()->cargarResidencia(null);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarAltaResidencia'){
     ResidenciaController::getInstance()->procesarAltaResidencia();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'mostrarResidencia' && !empty($_GET['id'])){
     ResidenciaController::getInstance()->mostrarResidencia(array('id' => $_GET['id'],'mensaje'=> null, 'exito'=> null));
}
else if(isset($_GET["action"]) && $_GET["action"] == 'verSemana' && !empty($_POST['idRS'])){
     ResidenciaController::getInstance()->verSemana($_POST['idRS']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'pujarSubasta' && !empty($_GET['idRS'])){
     ResidenciaController::getInstance()->verSemana($_GET['idRS']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'pujarSubasta' && !empty($_GET['idSubasta'])){
     ResidenciaController::getInstance()->verificarDatosPuja($_GET['idSubasta'],$_POST['puja']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'admin-login'){
  AdministradorController::getInstance()->adminLogin();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'admin-signup'){
  AdministradorController::getInstance()->adminSignup();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'user-login'){
  UsuarioController::getInstance()->userLogin();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'user-signup'){
     UsuarioController::getInstance()->userSignup();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'registraradmin'){
     AdministradorController::getInstance()->registrarAdmin(null);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'verPerfil'){
  UsuarioController::getInstance()->verPerfil();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'editarPerfil'){
  UsuarioController::getInstance()->editarPerfil(null);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cambiarRoldeUsuario'){
  UsuarioController::getInstance()->cambiarRol($_GET['idUsuario']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarEdicionPerfil'){
  UsuarioController::getInstance()->procesarEdicionPerfil();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'quieroCambiarRol'){
  UsuarioController::getInstance()->quieroCambiarTipoUsuario();
}

else if(isset($_GET["action"]) && $_GET["action"] == 'list-auctions'){
  AuctionsController::getInstance()->listAuctions();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'editarResidencia' && !empty($_GET['id'])){
   ResidenciaController::getInstance()->editarResidencia($_GET['id']);
}

else if(isset($_GET["action"]) && $_GET["action"] == 'eliminarResidencia' && !empty($_GET['id'])){
     ResidenciaController::getInstance()->eliminarResidencia($_GET['id']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarEdicionResidencia' && !empty($_GET['id'])){
   ResidenciaController::getInstance()->procesarEdicionResidencia($_GET['id']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cancelarEdicionResidencia' && !empty($_GET['id'])){
   ResidenciaController::getInstance()->cancelarEdicion($_GET['id']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'crearSubasta' && !empty($_GET['idResidencia'])){
   ResidenciaController::getInstance()->crearSubasta($_GET['idResidencia']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarCreacionSubasta' && !empty($_GET['idResidencia'])){
   AuctionsController::getInstance()->procesar_subasta($_GET['idResidencia'], $_POST['idSemana'],$_POST['base']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'finalizarSubasta' && !empty($_GET['idSubasta'])){
   AuctionsController::getInstance()->finalizarSubasta($_GET['idSubasta']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'verEstadoSubastas'){
   AuctionsController::getInstance()->estadoSubasta(null);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'sincronizar'){
   ResidenciaController::getInstance()->sincronizador();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'listarDirectas'){
   DirectaController::getInstance()->listarDirectasTodas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'comprarDirecta' && !empty($_GET['idRS']) && !empty($_GET['idUser'])){
   DirectaController::getInstance()->comprarSemana($_GET['idRS'],$_GET['idUser']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'listaPosibleHotsale'){
   HotsaleController::getInstance()->listarPosiblesHotsale();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'habilitarHotsale' && !empty($_GET['idRS'])){
   HotsaleController::getInstance()->habilitarHotsale($_GET['idRS']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarHotsale' && !empty($_GET['idRS'])){
   HotsaleController::getInstance()->procesarHotsale($_GET['idRS'],$_POST['precio']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarCreacionDirecta' && !empty($_GET['idResidencia'])){
   DirectaController::getInstance()->procesar_directa($_GET['idResidencia'], $_POST['idSemana'],$_POST['base']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'crearDirecta' && !empty($_GET['idResidencia'])){
   DirectaController::getInstance()->crearDirecta($_GET['idResidencia']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'listarSubastaInactivas'){
   AuctionsController::getInstance()->listarSubastasSinMontos();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cargarMontoSubasta' && !empty($_GET['idRS'])){
   AuctionsController::getInstance()->cargarMontoSubasta($_GET['idRS'],$_POST['base']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'buscarResidencia'){
   ResidenciaController::getInstance()->buscarResidencia();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'buscarSemanas'){
   ResidenciaController::getInstance()->buscarSemanas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'buscar'){
   ResidenciaController::getInstance()->buscar_semanas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'comprarHotsale'&& !empty($_GET['idRS']) && !empty($_GET['idUser'])){
    HotsaleController::getInstance()->comprarSemana($_GET['idRS'],$_GET['idUser']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'listarClientes'){
    UsuarioController::getInstance()->listarClientes();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'listarAdmins'){
    AdministradorController::getInstance()->listarAdmins();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'detallesCliente' && !empty($_GET["idUsuario"])){
    UsuarioController::getInstance()->detallesUsuario($_GET["idUsuario"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'detallesClienteHistorial' && !empty($_GET["idUsuario"])){
    UsuarioController::getInstance()->detallesUsuarioHistorial($_GET["idUsuario"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'detallesClienteEditarPerfil' && !empty($_GET["idUsuario"])){
    UsuarioController::getInstance()->detallesEditarPerfilUsuario(null,$_GET["idUsuario"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'buscarCliente'){
    UsuarioController::getInstance()->buscarCliente();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'habilitarDirecta' && !empty($_GET["idResidenciaSemana"])){
    DirectaController::getInstance()->activarDirecta($_GET['idResidenciaSemana']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'pasarSubasta' && !empty($_GET["idResidenciaSemana"])){
    DirectaController::getInstance()->cerrarDirecta($_GET['idResidenciaSemana']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'adjudicarSubasta' && !empty($_GET["idResidenciaSemana"]) && !empty($_GET["idSubasta"])){
    AuctionsController::getInstance()->adjudicarSubasta($_GET["idSubasta"],$_GET["idResidenciaSemana"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'pasarAHotsale' && !empty($_GET["idResidenciaSemana"])){
    AuctionsController::getInstance()->pasarAhotsale($_GET["idResidenciaSemana"]);
}



else if(isset($_GET["action"]) && $_GET["action"] == 'tarifas'){
    TarifasController::getInstance()->traerTarifas(null);

}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarEdicionTarifas'){
  TarifasController::getInstance()->procesarEdicionTarifas();
}


else if(isset($_GET["action"]) && $_GET["action"] == 'buscarSemanaAdmin'){
    AdministradorController::getInstance()->buscarSemanas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'sincronizarDirectas'){
   ResidenciaController::getInstance()->sincronizadorDirectas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'sincronizarSubastas'){
   ResidenciaController::getInstance()->sincronizadorSubastas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'sincronizarDirectas2021'){
   ResidenciaController::getInstance()->sincronizadorDirectas2021();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'panelSistema'){
  SistemaController::getInstance()->vistaPanel();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'sincronizarHotsales'){
   ResidenciaController::getInstance()->sincronizadorHotsales();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'solicitarPassword'){
    SistemaController::getInstance()->solicitarPassword($_POST['email']);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'cambiarPassword'){
    SistemaController::getInstance()->cambiarPassword();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'procesarCambioPassword' && !empty($_GET['email'])){
    SistemaController::getInstance()->procesar_cambioPassword();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'desactivarCuenta'){
    UsuarioController::getInstance()->desactivarCuenta();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'desactivarCuentaAdmin' && !empty($_GET["idAdministrador"])){
    AdministradorController::getInstance()->desactivarCuentaAdmin($_GET["idAdministrador"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'crearDirectasParaTodasLasResidencias'){
     ResidenciaController::getInstance()->crearSemanaDirectaParaTodasLasResidencias();
}

else if(isset($_GET["action"]) && $_GET["action"] == 'buscarSemanasAdmin'){
     AdministradorController::getInstance()->buscar_semanas();
}
else if(isset($_GET["action"]) && $_GET["action"] == 'editarSemana' && !empty($_GET["idRS"])&& !empty($_GET["idResidencia"])){
     ResidenciaSemanaController::getInstance()->editarSemana($_GET["idRS"],$_GET["idResidencia"]);
}
else if(isset($_GET["action"]) && $_GET["action"] == 'editar_semana' && !empty($_GET["idRS"])&& !empty($_GET["idResidencia"])){
     ResidenciaSemanaController::getInstance()->editar_semana($_GET["idRS"],$_GET["idResidencia"]);
}


else if(isset($_GET["action"]) && $_GET["action"] == 'verHistorialCompras'){
     UsuarioController::getInstance()->verHistorialDeCompras();
}





else{
	if(!isset($_SESSION['usuario']))
		Controller::getInstance()->vistaHome(null);
    else
	    Controller::getInstance()->vistaHome(array('user' => $_SESSION['usuario'], 'tipousuario' => $_SESSION['tipo']));
}


