<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\UsuarioController;
use App\Controllers\MateriaController;
use App\Controllers\InscripcionController;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\AlumnoMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\DepurarJsonMiddleware;
use Slim\MiddlewareDispatcher;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/programacion_3_2020/public');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$app->post('/users[/]', UsuarioController::class . ":Insert");

$app->post('/login[/]', UsuarioController::class . ":GenerarToken");

//AUTENTICADOS
$app->group('/', function (RouteCollectorProxy $group) {

    $group->post('materia[/]', MateriaController::class . ":Insert")->add(new AdminMiddleware);

    $group->post('inscripcion/{idMateria}[/]', InscripcionController::class . ":Insert")->add(new AlumnoMiddleware);

})->add(new AuthMiddleware);

$app->add(new DepurarJsonMiddleware);

//$app->addBodyParsingMiddleware();

$app->run();
?>