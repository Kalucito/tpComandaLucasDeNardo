<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
require_once("./controllers/UsuarioController.php");
require_once("./controllers/PedidoController.php");
require_once("./controllers/PedidoProductoController.php");
require_once("./controllers/MesaController.php");
require_once("./controllers/ProductoController.php");
require_once("./ClasesDeAutenticacion/MWParaAutenticar.php");
require_once("./db/AccesoDb.php");


// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->post('/login', \UsuarioController::class . ':LoginUsuario');


$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':cargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/listado', \PedidoProductoController::class . ':VerPedidosConTiempoDueño');
    $group->post('[/]', \PedidoController::class . ':cargarUno');
    $group->get('/productos', \PedidoProductoController::class . ':TraerTodos');
    $group->post('/carga', \PedidoProductoController::class . ':cargarUno');
    $group->post('/cargaImagen', \PedidoController::class . ':AgregarFoto');
    $group->get('/pendientes', \PedidoProductoController::class . ':TraerPedidosPendientes');
    $group->put('/tomarPendiente', \PedidoProductoController::class . ':CambiarEstadoPedido');
    $group->get('/estimado', \PedidoProductoController::class . ':TiempoEstimadoPedido');

})->add(MWParaAutenticar::class . ':VerificarUsuario');

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->post('[/]', \MesaController::class . ':cargarUno');
})->add(MWParaAutenticar::class . ':VerificarUsuario');

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->post('[/]', \ProductoController::class . ':cargarUno');
})->add(MWParaAutenticar::class . ':VerificarUsuario');

$app->run();
