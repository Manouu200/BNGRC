DROP DATABASE IF EXISTS BNGRC;
CREATE DATABASE BNGRC;
USE BNGRC;

CREATE TABLE IF NOT EXISTS besoins(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS unite(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS ville(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS sinistre(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_besoins INT NOT NULL,
    libellee VARCHAR(255) NOT NULL,
    id_ville INT NOT NULL,
    quantite INT NOT NULL,
    id_unite INT NOT NULL,
    FOREIGN KEY (id_besoins) REFERENCES besoins(id),
    FOREIGN KEY (id_ville) REFERENCES ville(id),
    FOREIGN KEY (id_unite) REFERENCES unite(id)
);
-- Table pour enregistrer les dons
CREATE TABLE IF NOT EXISTS dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    id_besoins INT NOT NULL,
    quantite INT NOT NULL,
    id_unite INT NOT NULL,
    libellee VARCHAR(255) DEFAULT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES ville(id),
    FOREIGN KEY (id_besoins) REFERENCES besoins(id),
    FOREIGN KEY (id_unite) REFERENCES unite(id)
);

-- Données de base réalistes
INSERT INTO besoins (nom) VALUES
    ('nature'),
    ('Matériaux'),
    ('Argent');

INSERT INTO unite (nom) VALUES
    ('Litre'),
    ('Kg'),
    ('Ariary'),
    ('unite');

INSERT INTO ville (nom) VALUES
    ('Tananarive'),
    ('Majunga'),
    ('Tamatave');

-- Vue présentant les sinistres sans exposer les identifiants
CREATE VIEW IF NOT EXISTS vue_sinistre AS
SELECT
    v.nom AS ville,
    b.nom AS besoin,
    s.libellee AS libellee,
    s.quantite AS quantite,
    u.nom AS unite
FROM sinistre s
JOIN ville v ON s.id_ville = v.id
JOIN besoins b ON s.id_besoins = b.id
JOIN unite u ON s.id_unite = u.id;

-- Vue présentant les dons avec les noms liés (ville, besoin, unité)
CREATE VIEW IF NOT EXISTS vue_dons AS
SELECT
    d.id,
    v.nom AS ville,
    b.nom AS besoin,
    d.libellee AS libellee,
    d.quantite AS quantite,
    u.nom AS unite,
    d.date AS date
FROM dons d
JOIN ville v ON d.id_ville = v.id
JOIN besoins b ON d.id_besoins = b.id
JOIN unite u ON d.id_unite = u.id;


