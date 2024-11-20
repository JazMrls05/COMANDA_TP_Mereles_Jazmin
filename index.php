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

require_once __DIR__ . '/Middlewares/AuthMWLogin.php';
require_once __DIR__ . '/Middlewares/AuthMWUsuario.php';
require_once __DIR__ . '/Middlewares/AuthMWToken.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosMesa.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosProducto.php';
require_once __DIR__ . '/Middlewares/AuthMWAtributosUsuario.php';




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
    $group->post('/productos', \ProductoController::class . ':Guardar')->add(AuthMWAt_Producto::class . ':__invoke');;
    $group->post('/mesas', \MesaController::class . ':Guardar')->add(AuthMWAt_Mesa::class . ':__invoke');;
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
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':VerTodos');
    $group->get('/{tipo}', \ProductoController::class . ':VerTodosPorTipo');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {

    $group->get('[/]', \PedidoController::class . ':VerTodos');
    $group->get('/estado/{estado}', \PedidoController::class . ':VerTodosPorEstado')->add(AuthMWUsuario::class . ':VerificarAdmin');
    $group->get('/verPorCodigo/{codigo}', \PedidoController::class . ':VerPorCodigo');
    $group->get('/demoras', \PedidoController::class . ':VerDemoras');
    $group->get('/pendientes', \PedidoController::class . ':VerPendientes');//->add(AuthMWUsuario::class . ':VerificarEmpleado');
    $group->get('/listosParaServir', \PedidoController::class . ':VerListosParaServir')->add(AuthMWUsuario::class . ':VerificarMozo');

    $group->post('[/]', \PedidoController::class . ':Guardar')->add(AuthMWUsuario::class . ':VerificarMozo'); 
    $group->post('/csv', \PedidoController::class . ':GuardarDesdeCSV')->add(AuthMWUsuario::class . ':VerificarMozo'); 

    $group->put('/estado/preparando/{codigo}', \PedidoController::class . ':ModificarEstadoPreparando')->add(AuthMWUsuario::class . ':VerificarEmpleado');
    $group->put('/estado/servir/{codigo}', \PedidoController::class . ':ModificarEstadoListo')->add(AuthMWUsuario::class . ':VerificarEmpleado');

});



$app->group('/mesas', function (RouteCollectorProxy $group) {
    /* Estados
    1- Con cliente esperando pedido
    2- Con cliente comiendo
    3- Con cliente pagando
    */
    $group->get('[/]', \MesaController::class . ':VerTodos');
    $group->get('/estado/{estado}', \MesaController::class . ':VerTodosPorEstado')->add(AuthMWUsuario::class . ':VerificarAdmin'); // Estados 1, 2, 3, 4, todos

    $group->put('/estado/{estado}/{codigoMesa}', \MesaController::class . ':ModificarEstado')->add(AuthMWUsuario::class . ':VerificarMozo'); //Estados 1, 2 y 3
    $group->put('/estado/cerrar/{codigoMesa}', \MesaController::class . ':CerrarMesa')->add(AuthMWUsuario::class . ':VerificarAdmin');
});

$app->get('/cliente', \PedidoController::class . ':VistaCliente');

#region JWT
$app->group('/jwt', function (RouteCollectorProxy $group)
{
    $group->post('/crearToken', function (Request $request, Response $response) {
    })->add(AuthMWLogin::class);
});
#endregion

$app->run();