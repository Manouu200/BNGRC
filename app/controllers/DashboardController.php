<?php

namespace app\controllers;

use flight\Engine;
use app\models\SinistreModel;

class DashboardController
{
    protected Engine $app;
    protected SinistreModel $sinistreModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->sinistreModel = new SinistreModel($this->app->db());
    }

    public function showDashboard()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sinistres = $this->sinistreModel->get();

        // Récupérer les dons directement depuis la vue SQL `vue_dons` sans créer de nouveau model
        $dons = [];
        try {
            $stmt = $this->app->db()->query('SELECT * FROM vue_dons');
            $dons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Ne pas casser l'affichage si la vue n'existe pas encore; on peut logger si besoin
        }

        $this->app->render('dashboard.php', ['sinistres' => $sinistres, 'dons' => $dons]);
    }
}
