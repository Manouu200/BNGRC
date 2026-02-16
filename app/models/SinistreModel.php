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
        $sql = "SELECT s.id, s.id_objet, o.libellee, o.id_besoins, b.nom AS besoin, s.id_ville, v.nom AS ville, s.quantite, o.id_unite AS id_unite, u.nom AS unite, s.date AS date, s.id_etat, e.nom AS etat
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_ville v ON s.id_ville = v.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            JOIN BNGRC_etat e ON s.id_etat = e.id
            ORDER BY s.date ASC, s.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT s.id, s.id_objet, o.libellee, o.id_besoins, b.nom AS besoin, s.id_ville, v.nom AS ville, s.quantite, o.id_unite AS id_unite, u.nom AS unite, s.date AS date, s.id_etat, e.nom AS etat
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_ville v ON s.id_ville = v.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            JOIN BNGRC_etat e ON s.id_etat = e.id
            WHERE s.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
    public function insertByObjet(int $id_objet, int $id_ville, int $quantite, ?string $date = null): int
    {
        if ($date !== null) {
            $sql = "INSERT INTO BNGRC_sinistre (id_objet, id_ville, quantite, date) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet, $id_ville, $quantite, $date]);
        } else {
            $sql = "INSERT INTO BNGRC_sinistre (id_objet, id_ville, quantite) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet, $id_ville, $quantite]);
        }

        return (int)$this->db->lastInsertId();
    }

    public function updateByObjet(int $id, int $id_objet, int $id_ville, int $quantite): bool
    {
        $sql = "UPDATE BNGRC_sinistre SET id_objet = ?, id_ville = ?, quantite = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_objet, $id_ville, $quantite, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM BNGRC_sinistre WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function updateQuantiteEtat(int $id, int $quantite, ?int $id_etat = null): bool
    {
        $fields = ['quantite = ?'];
        $params = [$quantite];

        if ($id_etat !== null) {
            $fields[] = 'id_etat = ?';
            $params[] = $id_etat;
        }

        $params[] = $id;
        $sql = 'UPDATE BNGRC_sinistre SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}
