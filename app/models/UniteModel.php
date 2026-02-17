<?php

namespace app\models;

use PDO;

class UniteModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function get(): array
    {
        $sql = "SELECT id, nom FROM BNGRC_unite ORDER BY nom";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT id, nom FROM BNGRC_unite WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function insert(string $nom): int
    {
        $sql = "INSERT INTO BNGRC_unite (nom) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nom]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nom): bool
    {
        $sql = "UPDATE BNGRC_unite SET nom = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nom, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM BNGRC_unite WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
