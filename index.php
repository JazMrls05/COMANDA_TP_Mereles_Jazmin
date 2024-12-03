<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/DB/AccesoDatos.php';

require_once __DIR__ . '/Controladores/UsuarioController.php';
require_once __DIR__ . '/Controladores/ProductoController.php';
require_once __DIR__ . '/Controladores/MesaController.php';
require_once __DIR__ . '/Controladores/PedidoController.php';
require_once __DIR__ . '/Controladores/ComandaController.php';
require_once __DIR__ . '/Controladores/EncuestaController.php';

require_once __DIR__ . '/Middlewares/AuthMWLogin.php';
require_once __DIR__ . '/Middlewares/AuthMWUsuario.php';
require_once __DIR__ . '/Middlewares/AuthMWToken.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosMesa.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosProducto.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosUsuario.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosPedido.php';
require_once __DIR__ . '/Middlewares/AuthMWEncuesta.php';

require_once __DIR__ . '/Modelos/SerializadoraCSV.php';
require_once __DIR__ . '/Modelos/ManejadorPDF.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes


#region grupos solicitudes SOLO ADMIN
$app->group('/insertar', function (RouteCollectorProxy $group) {
    $group->post('/usuarios', \UsuarioController::class . ':Guardar')->add(AuthMWAt_Usuario::class . ':__invoke');
    $group->post('/productos', \ProductoController::class . ':Guardar')->add(AuthMWAt_Producto::class . ':__invoke');
    $group->post('/mesas', \MesaController::class . ':Guardar')->add(AuthMWAt_Mesa::class . ':__invoke');
})->add(AuthMWUsuario::class . ':VerificarAdmin')->add(AuthMWToken::class . ':__invoke');

$app->group('/insertar/csv', function (RouteCollectorProxy $group) {
    $group->post('/usuarios', \UsuarioController::class . ':GuardarDesdeCSV');
    $group->post('/productos', \ProductoController::class . ':GuardarDesdeCSV');
    $group->post('/mesas', \MesaController::class . ':GuardarDesdeCSV');
})->add(AuthMWUsuario::class . ':VerificarAdmin');

$app->group('/eliminar', function (RouteCollectorProxy $group) {
    $group->delete('/usuarios/{id}', \UsuarioController::class . ':EliminarPorID');
    $group->delete('/productos/{id}', \ProductoController::class . ':EliminarPorID');
    $group->delete('/pedidos/{codigo}', \PedidoController::class . ':EliminarPorCodigo');
    $group->delete('/mesas/{codigo}', \MesaController::class . ':EliminarPorCodigo');
})->add(AuthMWUsuario::class . ':VerificarAdmin');
#endregion

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':VerTodos');
    $group->get('/{id}', \UsuarioController::class . ':VerPorID');
    $group->get('/sector/{sector}', \UsuarioController::class . ':VerPorSector');

    $group->put('/habilitar/{id}', \UsuarioController::class . ':HabilitarUsuario')->add(AuthMWUsuario::class . ':VerificarAdmin');
    $group->put('/inhabilitar/{id}', \UsuarioController::class . ':InhabilitarUsuario')->add(AuthMWUsuario::class . ':VerificarAdmin');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':VerTodos');
    $group->get('/{tipo}', \ProductoController::class . ':VerTodosPorTipo');
    $group->post('/crearCSV', \SerializadoraCSV::class . ':LeerDB');
    $group->post('/guardarProductoCSV', \SerializadoraCSV::class . ':GuardarDatos');
    $group->get('/CSV/descargar', \ProductoController::class . ':descargarCSV');
    $group->get('/PDF/descargar', \ManejadorPDF::class . ':descargarProductos');
});

$app->group('/pedido', function (RouteCollectorProxy $group) {

    /*
     * Estados de los pedidos:
    1: Pendiente
    2: En preparación
    3: listo para servir
    "todos"
     */
    $group->get('[/]', \PedidoController::class . ':VerTodos');
    $group->get('/demoras', \PedidoController::class . ':VerDemoras');
    $group->get('/pendientes', \PedidoController::class . ':VerPendientes'); // punto 3a y 6a
    $group->get('/listosParaServir', \PedidoController::class . ':VerListosParaServir')->add(AuthMWUsuario::class . ':VerificarMozo');

    $group->post('[/]', \PedidoController::class . ':Guardar')->add(AuthMWAt_pedido::class . ':__invoke')->add(AuthMWUsuario::class . ':VerificarMozo'); // punto 1 y 2
    $group->post('/csv', \PedidoController::class . ':GuardarDesdeCSV')->add(AuthMWUsuario::class . ':VerificarMozo'); 

    $group->put('/estado/preparando/{codigo}', \PedidoController::class . ':ModificarEstadoPreparando')->add(AuthMWUsuario::class . ':VerificarEmpleado'); //punto 3b
    $group->put('/estado/servir/{codigo}', \PedidoController::class . ':ModificarEstadoListo')->add(AuthMWUsuario::class . ':VerificarEmpleado'); //punto 6b
    
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
    /* Estados
    1- Con cliente esperando pedido
    2- Con cliente comiendo
    */
    $group->get('[/]', \MesaController::class . ':VerTodos');
    $group->get('/estados', \MesaController::class . ':VerEstados')->add(AuthMWUsuario::class . ':VerificarAdmin'); 
    $group->get('/masUsada', \MesaController::class . ':VerMasUsada')->add(AuthMWUsuario::class . ':VerificarAdmin');


    $group->put('/estado/{estado}/{codigoMesa}', \MesaController::class . ':ModificarEstado')->add(AuthMWUsuario::class . ':VerificarMozo'); //Estados 1 y 2
    $group->put('/estado/cobrar/{codigoMesa}', \MesaController::class . ':CobrarMesa')->add(AuthMWUsuario::class . ':VerificarMozo');
    $group->put('/estado/cerrar/{codigoMesa}', \MesaController::class . ':CerrarMesa')->add(AuthMWUsuario::class . ':VerificarAdmin');
});

$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/ver', \PedidoController::class . ':VistaCliente');
    $group->post('/realizarEncuesta', \EncuestaController::class . ':Guardar')->add(AuthMWEncuesta::class . ':__invoke');
});

$app->post('/crearComandas/{cantidadComandas}', \ComandaController::class . ':Guardar'); // No se pueden crear mas de 5 comandas a las vez
$app->post('/cobrarCuenta', \ComandaController::class . ':CobrarCuenta')->add(AuthMWUsuario::class . ':VerificarMozo');
$app->get('/mejoresComentarios', \EncuestaController::class . ':VerMejoresComentarios')->add(AuthMWUsuario::class . ':VerificarAdmin');

#region JWT
$app->group('/jwt', function (RouteCollectorProxy $group)
{
    $group->post('/crearToken', function (Request $request, Response $response) {
    })->add(AuthMWLogin::class);
});
#endregion

$app->run();