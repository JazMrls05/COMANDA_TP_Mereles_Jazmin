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

require_once __DIR__ . '/Middlewares/AuthMwAdmin.php';


// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':VerTodos');
    $group->get('/{id}', \UsuarioController::class . ':VerPorID');
    $group->get('/sector/{sector}', \UsuarioController::class . ':VerPorSector');
    $group->post('[/]', \UsuarioController::class . ':Guardar');
    $group->delete('/{id}', \UsuarioController::class . ':EliminarPorID');
})->add(\AuthMWAdmin::class . ":__invoke");

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':VerTodos');
    $group->get('/{tipo}', \ProductoController::class . ':VerTodosPorTipo');
    $group->post('[/]', \ProductoController::class . ':Guardar');
    $group->delete('/{id}', \ProductoController::class . ':EliminarPorID');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':VerTodos');
    $group->get('/estado/{estado}', \PedidoController::class . ':VerTodosPorEstado');
    $group->get('/{codigo}', \PedidoController::class . ':VerPorCodigo');
    $group->post('[/]', \PedidoController::class . ':Guardar');
    $group->delete('/{codigo}', \PedidoController::class . ':EliminarPorCodigo');
});


$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':VerTodos');
    $group->get('/estado/{estado}', \MesaController::class . ':VerTodosPorEstado');
    $group->get('/tiempo', \MesaController::class . ':VerTiempoRestante');
    $group->post('[/]', \MesaController::class . ':Guardar');
    $group->delete('/{codigo}', \MesaController::class . ':EliminarPorCodigo');
});



$app->run();