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
        $sql = "SELECT d.id, v.nom AS ville, b.nom AS besoin, o.libellee, d.quantite, u.nom AS unite, d.date
            FROM BNGRC_dons d
            JOIN BNGRC_ville v ON d.id_ville = v.id
            JOIN BNGRC_objet o ON d.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            ORDER BY d.date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForDispatch(): array
    {
        $sql = "SELECT d.id, o.libellee AS libellee, d.quantite, v.nom AS ville, b.nom AS besoin, u.nom AS unite
            FROM BNGRC_dons d
            JOIN BNGRC_ville v ON d.id_ville = v.id
            JOIN BNGRC_objet o ON d.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            ORDER BY d.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne la liste des dons dont le besoin est 'Argent' (quantite représente un montant en Ariary).
     * Les résultats sont ordonnés par id.
     * @return array
     */
    public function getArgentDons(): array
    {
        $sql = "SELECT d.id, d.quantite
            FROM BNGRC_dons d
            JOIN BNGRC_objet o ON d.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            WHERE LOWER(b.nom) = 'argent'
            ORDER BY d.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT d.id, v.nom AS ville, b.nom AS besoin, o.libellee, d.quantite, u.nom AS unite, d.date
                FROM BNGRC_dons d
                JOIN BNGRC_ville v ON d.id_ville = v.id
                JOIN BNGRC_objet o ON d.id_objet = o.id
                JOIN BNGRC_besoins b ON o.id_besoins = b.id
                JOIN BNGRC_unite u ON o.id_unite = u.id
                WHERE d.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
    public function insertByObjet(int $id_ville, int $id_objet, int $quantite, ?string $date = null): int
    {
        if ($date !== null) {
            $sql = "INSERT INTO BNGRC_dons (id_ville, id_objet, quantite, date) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_ville, $id_objet, $quantite, $date]);
        } else {
            $sql = "INSERT INTO BNGRC_dons (id_ville, id_objet, quantite) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_ville, $id_objet, $quantite]);
        }
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, int $id_ville, int $id_besoins, int $id_unite, int $quantite, ?string $libellee = null, ?string $date = null): bool
    {
        // Keep legacy signature but update through objet join requires providing objet id via controller
        if ($date === null) {
            $sql = "UPDATE BNGRC_dons SET id_ville = ?, quantite = ? WHERE id = ?";
            $params = [$id_ville, $quantite, $id];
        } else {
            $sql = "UPDATE BNGRC_dons SET id_ville = ?, quantite = ?, date = ? WHERE id = ?";
            $params = [$id_ville, $quantite, $date, $id];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM BNGRC_dons WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function updateQuantite(int $id, int $quantite): bool
    {
        $sql = "UPDATE BNGRC_dons SET quantite = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$quantite, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Retourne la somme des quantités pour les dons dont le besoin est 'Argent'.
     * La comparaison sur le nom du besoin est insensible à la casse.
     * Retourne 0 si aucun don correspondant.
     *
     * @return float
     */
    public function getTotalArgent(): float
    {
        $sql = "SELECT SUM(d.quantite) AS total
            FROM BNGRC_dons d
            JOIN BNGRC_objet o ON d.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            WHERE LOWER(b.nom) = 'argent'";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false || $row['total'] === null) {
            return 0.0;
        }
        return (float)$row['total'];
    }

    /**
     * Retourne true si des dons existent déjà pour cet objet (quantite > 0).
     * @param int $id_objet
     * @return bool
     */
    public function existsByObjet(int $id_objet): bool
    {
        $sql = "SELECT 1 FROM BNGRC_dons WHERE id_objet = ? AND quantite > 0 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_objet]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
