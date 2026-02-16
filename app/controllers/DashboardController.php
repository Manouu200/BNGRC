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
        $this->app->render('dashboard.php', ['sinistres' => $sinistres]);
    }
}
