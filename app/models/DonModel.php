<?php

namespace app\models;

use PDO;

class DonModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function get(): array
    {
        $sql = "SELECT d.id, v.nom AS ville, b.nom AS besoin, d.libellee, d.quantite, u.nom AS unite, d.date
                FROM dons d
                JOIN ville v ON d.id_ville = v.id
                JOIN besoins b ON d.id_besoins = b.id
                JOIN unite u ON d.id_unite = u.id
                ORDER BY d.date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT d.id, v.nom AS ville, b.nom AS besoin, d.libellee, d.quantite, u.nom AS unite, d.date
                FROM dons d
                JOIN ville v ON d.id_ville = v.id
                JOIN besoins b ON d.id_besoins = b.id
                JOIN unite u ON d.id_unite = u.id
                WHERE d.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function insert(int $id_ville, int $id_besoins, int $id_unite, int $quantite, ?string $libellee = null): int
    {
        $sql = "INSERT INTO dons (id_ville, id_besoins, quantite, id_unite, libellee) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_ville, $id_besoins, $quantite, $id_unite, $libellee]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, int $id_ville, int $id_besoins, int $id_unite, int $quantite, ?string $libellee = null, ?string $date = null): bool
    {
        if ($date === null) {
            $sql = "UPDATE dons SET id_ville = ?, id_besoins = ?, id_unite = ?, quantite = ?, libellee = ? WHERE id = ?";
            $params = [$id_ville, $id_besoins, $id_unite, $quantite, $libellee, $id];
        } else {
            $sql = "UPDATE dons SET id_ville = ?, id_besoins = ?, id_unite = ?, quantite = ?, libellee = ?, date = ? WHERE id = ?";
            $params = [$id_ville, $id_besoins, $id_unite, $quantite, $libellee, $date, $id];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM dons WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
