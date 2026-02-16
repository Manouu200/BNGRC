<?php

use flight\Engine;
use flight\net\Router;

use app\controllers\InscriptionController;
use app\controllers\ConnexionController;
use app\controllers\HomeController;
use app\controllers\ExplorerController;
use app\controllers\AuthController;
use app\controllers\ExchangeController;

/** @var Router $router */
/** @var Engine $app */

// Affiche la page d'inscription à la racine
$router->get('/', function () use ($app) {
    $controller = new HomeController($app);
    $controller->showHome();
});

$router->post('/sinistre/create', function () use ($app) {
    $controller = new HomeController($app);
    $controller->createSinistre();
});

$router->get('/dashboard', function () use ($app) {
    $controller = new \app\controllers\DashboardController($app);
    $controller->showDashboard();
});

$router->get('/dons', function () use ($app) {
    $controller = new \app\controllers\DonsController($app);
    $controller->showForm();
});

$router->get('/achat', function () use ($app) {
    $controller = new \app\controllers\AchatController($app);
    $controller->showAchat();
});

$router->post('/achat/buy', function () use ($app) {
    $controller = new \app\controllers\AchatController($app);
    $controller->buy();
});

$router->post('/dons/create', function () use ($app) {
    $controller = new \app\controllers\DonsController($app);
    $controller->createDon();
});

$router->post('/dons/dispatch', function () use ($app) {
    $controller = new \app\controllers\DonsController($app);
    $controller->dispatchDons();
});
