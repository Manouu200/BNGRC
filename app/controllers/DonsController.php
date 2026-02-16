<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\VilleModel;
use app\models\UniteModel;
use app\models\DonModel;

class DonsController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;
    protected UniteModel $uniteModel;
    protected DonModel $donModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
        $this->uniteModel = new UniteModel($this->app->db());
        $this->donModel = new DonModel($this->app->db());
    }

    public function showForm()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $besoins = $this->besoinModel->get();
        $villes = $this->villeModel->get();
        $unites = $this->uniteModel->get();

        $this->app->render('dons.php', ['besoins' => $besoins, 'villes' => $villes, 'unites' => $unites]);
    }

    public function createDon()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect('/dons');
            return;
        }

        $id_besoin = isset($_POST['type_besoin']) ? (int)$_POST['type_besoin'] : 0;
        $id_ville = isset($_POST['ville']) ? (int)$_POST['ville'] : 0;
        $id_unite = isset($_POST['unite']) ? (int)$_POST['unite'] : 0;
        $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
        $libellee = isset($_POST['libellee']) ? trim((string)$_POST['libellee']) : null;
        $date = isset($_POST['date']) && $_POST['date'] !== '' ? $_POST['date'] : null;

        if ($id_besoin <= 0 || $id_ville <= 0 || $id_unite <= 0) {
            $this->app->redirect('/dons?created=0');
            return;
        }

        try {
            $this->donModel->insert($id_ville, $id_besoin, $id_unite, $quantite, $libellee);
            $this->app->redirect('/dons?created=1');
        } catch (\Throwable $e) {
            $this->app->redirect('/dons?created=0');
        }
    }
}
