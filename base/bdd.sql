DROP DATABASE IF EXISTS BNGRC;
CREATE DATABASE BNGRC;
USE BNGRC;

CREATE TABLE IF NOT EXISTS BNGRC_besoins(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS BNGRC_unite(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS BNGRC_ville(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

-- Table pour représenter l'état d'un besoin/don (insatisfait, satisfait)
CREATE TABLE IF NOT EXISTS BNGRC_etat(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS BNGRC_sinistre(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_besoins INT NOT NULL,
    libellee VARCHAR(255) NOT NULL,
    id_ville INT NOT NULL,
    quantite INT NOT NULL,
    id_unite INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_etat INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_besoins) REFERENCES BNGRC_besoins(id),
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_unite) REFERENCES BNGRC_unite(id),
    FOREIGN KEY (id_etat) REFERENCES BNGRC_etat(id)
);
-- Table pour enregistrer les dons
CREATE TABLE IF NOT EXISTS BNGRC_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    id_besoins INT NOT NULL,
    quantite INT NOT NULL,
    id_unite INT NOT NULL,
    libellee VARCHAR(255) DEFAULT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_besoins) REFERENCES BNGRC_besoins(id),
    FOREIGN KEY (id_unite) REFERENCES BNGRC_unite(id)
);

-- Données de base réalistes
INSERT INTO BNGRC_besoins (nom) VALUES
    ('nature'),
    ('Matériaux'),
    ('Argent');

INSERT INTO BNGRC_unite (nom) VALUES
    ('Litre'),
    ('Kg'),
    ('Ariary'),
    ('unite');

INSERT INTO BNGRC_ville (nom) VALUES
    ('Tananarive'),
    ('Majunga'),
    ('Tamatave');

-- Valeurs par défaut pour `etat`
INSERT INTO BNGRC_etat (nom) VALUES
    ('insatisfait'),
    ('satisfait');

-- Vue présentant les sinistres sans exposer les identifiants
CREATE VIEW IF NOT EXISTS BNGRC_vue_sinistre AS
SELECT
    v.nom AS ville,
    b.nom AS besoin,
    s.libellee AS libellee,
    s.quantite AS quantite,
    u.nom AS unite,
    s.date AS date,
    e.nom AS etat
FROM BNGRC_sinistre s
JOIN BNGRC_ville v ON s.id_ville = v.id
JOIN BNGRC_besoins b ON s.id_besoins = b.id
JOIN BNGRC_unite u ON s.id_unite = u.id;

-- Vue présentant les dons avec les noms liés (ville, besoin, unité)
CREATE VIEW IF NOT EXISTS BNGRC_vue_dons AS
SELECT
    d.id,
    v.nom AS ville,
    b.nom AS besoin,
    d.libellee AS libellee,
    d.quantite AS quantite,
    u.nom AS unite,
    d.date AS date
FROM BNGRC_dons d
JOIN BNGRC_ville v ON d.id_ville = v.id
JOIN BNGRC_besoins b ON d.id_besoins = b.id
JOIN BNGRC_unite u ON d.id_unite = u.id;


