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
        $sql = "SELECT s.id, s.id_objet, o.libellee, o.id_besoins, b.nom AS besoin, s.id_ville, v.nom AS ville, s.quantite, s.ordre, s.quantite_initiale, o.id_unite AS id_unite, u.nom AS unite, o.prix_unitaire AS prix_unitaire, s.date AS date, s.id_etat, e.nom AS etat
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            JOIN BNGRC_besoins b ON o.id_besoins = b.id
            JOIN BNGRC_ville v ON s.id_ville = v.id
            JOIN BNGRC_unite u ON o.id_unite = u.id
            JOIN BNGRC_etat e ON s.id_etat = e.id
            ORDER BY s.ordre ASC, s.date ASC, s.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT s.id, s.id_objet, o.libellee, o.id_besoins, b.nom AS besoin, s.id_ville, v.nom AS ville, s.quantite, s.ordre, o.id_unite AS id_unite, u.nom AS unite, o.prix_unitaire AS prix_unitaire, s.date AS date, s.id_etat, e.nom AS etat
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
    public function insertByObjet(int $id_objet, int $id_ville, int $quantite, ?string $date = null, int $ordre = 0): int
    {
        if ($date !== null) {
            $sql = "INSERT INTO BNGRC_sinistre (id_objet, id_ville, quantite, ordre, quantite_initiale, date) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet, $id_ville, $quantite, $ordre, $quantite, $date]);
        } else {
            $sql = "INSERT INTO BNGRC_sinistre (id_objet, id_ville, quantite, ordre, quantite_initiale) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_objet, $id_ville, $quantite, $ordre, $quantite]);
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

    public function updateQuantiteEtat(int $id, ?int $quantite = null, ?int $id_etat = null): bool
    {
        $fields = [];
        $params = [];

        if ($quantite !== null) {
            $fields[] = 'quantite = ?';
            $params[] = $quantite;
        }

        if ($id_etat !== null) {
            $fields[] = 'id_etat = ?';
            $params[] = $id_etat;
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = 'UPDATE BNGRC_sinistre SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Retourne le montant total (quantite_initiale * prix) pour tous les sinistres disposant d'un prix.
     * Utilise quantite_initiale pour avoir le montant total original des besoins.
     */
    public function getTotalMontantGlobal(): float
    {
        $sql = "SELECT SUM(s.quantite_initiale * o.prix_unitaire) AS total
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            WHERE o.prix_unitaire IS NOT NULL";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false || $row['total'] === null) {
            return 0.0;
        }
        return (float)$row['total'];
    }

    /**
     * Retourne un tableau des montants regroupes par etat (indexe par id_etat).
     * Pour les besoins satisfaits (id_etat=2): calcul basé sur (quantite_initiale - quantite) * prix
     * Pour les besoins insatisfaits (id_etat=1): calcul basé sur quantite restante * prix
     */
    public function getMontantsParEtat(): array
    {
        // Montant restant (insatisfait) = quantite actuelle * prix
        $sqlRestant = "SELECT SUM(s.quantite * o.prix_unitaire) AS total
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            WHERE o.prix_unitaire IS NOT NULL";
        $stmt = $this->db->query($sqlRestant);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRestant = ($row !== false && $row['total'] !== null) ? (float)$row['total'] : 0.0;

        // Montant satisfait = (quantite_initiale - quantite) * prix = ce qui a été dispatché
        $sqlSatisfait = "SELECT SUM((s.quantite_initiale - s.quantite) * o.prix_unitaire) AS total
            FROM BNGRC_sinistre s
            JOIN BNGRC_objet o ON s.id_objet = o.id
            WHERE o.prix_unitaire IS NOT NULL";
        $stmt = $this->db->query($sqlSatisfait);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalSatisfait = ($row !== false && $row['total'] !== null) ? (float)$row['total'] : 0.0;

        return [
            1 => $totalRestant,   // insatisfait = quantité restante
            2 => $totalSatisfait, // satisfait = quantité dispatchée
        ];
    }

    /**
     * Compatibilite : total restant = montant regroupe pour l'etat 1 (non satisfait).
     */
    public function getTotalMontantRestant(): float
    {
        $totauxParEtat = $this->getMontantsParEtat();
        return $totauxParEtat[1] ?? 0.0;
    }
}
