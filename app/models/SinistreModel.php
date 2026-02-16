<?php

namespace app\models;

use PDO;

class SinistreModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function get(): array
    {
        $sql = "SELECT s.id, s.id_besoins, b.nom AS besoin, s.libellee, s.id_ville, v.nom AS ville, s.quantite, s.id_unite, u.nom AS unite, s.date AS date, s.id_etat, e.nom AS etat
            FROM sinistre s
            JOIN besoins b ON s.id_besoins = b.id
            JOIN ville v ON s.id_ville = v.id
            JOIN unite u ON s.id_unite = u.id
            JOIN etat e ON s.id_etat = e.id
            ORDER BY s.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT s.id, s.id_besoins, b.nom AS besoin, s.libellee, s.id_ville, v.nom AS ville, s.quantite, s.id_unite, u.nom AS unite, s.date AS date, s.id_etat, e.nom AS etat
            FROM sinistre s
            JOIN besoins b ON s.id_besoins = b.id
            JOIN ville v ON s.id_ville = v.id
            JOIN unite u ON s.id_unite = u.id
            JOIN etat e ON s.id_etat = e.id
            WHERE s.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function insert(int $id_besoins, string $libellee, int $id_ville, int $quantite, int $id_unite): int
    {
        $sql = "INSERT INTO sinistre (id_besoins, libellee, id_ville, quantite, id_unite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_besoins, $libellee, $id_ville, $quantite, $id_unite]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, int $id_besoins, string $libellee, int $id_ville, int $quantite, int $id_unite): bool
    {
        $sql = "UPDATE sinistre SET id_besoins = ?, libellee = ?, id_ville = ?, quantite = ?, id_unite = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_besoins, $libellee, $id_ville, $quantite, $id_unite, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM sinistre WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
