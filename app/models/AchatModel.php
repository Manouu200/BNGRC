<?php

namespace app\models;

use PDO;

class AchatModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Insère un achat pour l'objet fourni. Retourne l'id inséré.
     * @param int $id_objet
     * @param string|null $date
     * @return int
     */
    public function insert(int $id_objet, ?string $date = null): int
    {
        if ($date !== null && $date !== '') {
            $sql = "INSERT INTO BNGRC_achat (id_objet, date) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet, $date]);
        } else {
            $sql = "INSERT INTO BNGRC_achat (id_objet) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet]);
        }
        return (int)$this->db->lastInsertId();
    }

    /**
     * Retourne la liste des achats avec informations d'objet et besoin.
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT a.id, a.id_objet, a.date, o.libellee AS objet, b.nom AS besoin, u.nom AS unite, o.prix_unitaire AS prix_unitaire
            FROM BNGRC_achat a
            JOIN BNGRC_objet o ON a.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            ORDER BY a.date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un achat par son id.
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT a.id, a.date, o.libellee AS objet, b.nom AS besoin, u.nom AS unite
            FROM BNGRC_achat a
            JOIN BNGRC_objet o ON a.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            WHERE a.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * Retourne true si un achat existe déjà pour cet objet.
     * @param int $id_objet
     * @return bool
     */
    public function existsByObjet(int $id_objet): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM BNGRC_achat WHERE id_objet = ? LIMIT 1');
        $stmt->execute([$id_objet]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
