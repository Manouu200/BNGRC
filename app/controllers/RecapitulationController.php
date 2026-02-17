<?php

namespace app\controllers;

use flight\Engine;
use app\models\SinistreModel;
use app\models\DonModel;

class RecapitulationController
{
    protected Engine $app;
    protected SinistreModel $sinistreModel;
    protected DonModel $donModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->donModel = new DonModel($this->app->db());
    }

    public function show(): void
    {
        $stats = $this->collectStats();
        $stats['lastUpdated'] = date('Y-m-d H:i:s');
        
        // Récupérer tous les besoins (sinistres) pour le tableau
        $stats['besoins'] = $this->sinistreModel->get();
        
        // Récupérer tous les dons pour le tableau
        $stats['dons'] = $this->donModel->get();
        
        $this->app->render('recapitulation.php', $stats);
    }

    public function stats(): void
    {
        header('Content-Type: application/json');
        try {
            $stats = $this->collectStats();
            $stats['lastUpdated'] = date('Y-m-d H:i:s');
            $stats['success'] = true;
            echo json_encode($stats);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function collectStats(): array
    {
        $totauxParEtat = $this->sinistreModel->getMontantsParEtat();
        $totalBesoin = $this->sinistreModel->getTotalMontantGlobal();
        $totalSatisfait = $totauxParEtat[2] ?? 0.0;
        $totalRestant = $totauxParEtat[1] ?? 0.0;

        return [
            'totalBesoins' => $totalBesoin,
            'totalSatisfaits' => $totalSatisfait,
            'totalRestants' => $totalRestant,
        ];
    }
}
