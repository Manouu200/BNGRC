<?php

namespace app\models;

use PDO;

class ObjetModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $sql = "SELECT o.id, o.id_besoins, b.nom AS besoin, o.libellee, o.id_unite, u.nom AS unite, o.prix_unitaire FROM BNGRC_objet o JOIN BNGRC_besoins b ON o.id_besoins = b.id JOIN BNGRC_unite u ON o.id_unite = u.id ORDER BY o.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT o.id, o.id_besoins, b.nom AS besoin, o.libellee, o.id_unite, u.nom AS unite, o.prix_unitaire FROM BNGRC_objet o JOIN BNGRC_besoins b ON o.id_besoins = b.id JOIN BNGRC_unite u ON o.id_unite = u.id WHERE o.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
