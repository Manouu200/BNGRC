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
    $app->render('home.php');
});

// // Page d'accueil après connexion/inscription
// $router->get('/home', [HomeController::class, 'showHome']);
