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

CREATE TABLE IF NOT EXISTS BNGRC_objet(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_besoins INT NOT NULL,
    libellee VARCHAR(255) NOT NULL,
    id_unite INT NOT NULL,
    prix_unitaire DECIMAL(12,2) DEFAULT NULL,
    FOREIGN KEY (id_besoins) REFERENCES BNGRC_besoins(id),
    FOREIGN KEY (id_unite) REFERENCES BNGRC_unite(id)
);

CREATE TABLE IF NOT EXISTS BNGRC_sinistre(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    id_ville INT NOT NULL,
    quantite INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_etat INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id),
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_etat) REFERENCES BNGRC_etat(id)
);
-- Table pour enregistrer les dons
CREATE TABLE IF NOT EXISTS BNGRC_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    id_objet INT NOT NULL,
    quantite INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id)
);

-- Table pour enregistrer les achats (réalisés à partir des dons 'Argent')
CREATE TABLE IF NOT EXISTS BNGRC_achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id)
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

INSERT INTO BNGRC_objet (id_besoins, libellee, id_unite, prix_unitaire) VALUES
    (1, 'Riz blanc', 2, 3000.00),
    (1, 'Huile alimentaire', 1, 12000.00),
    (1, 'Eau potable', 1, 1500.00),
    (1, 'Pâtes alimentaires', 2, 4000.00),
    (1, 'Lait en poudre', 2, 18000.00),
    (2, 'Bâches de protection', 4, 25000.00),
    (2, 'Kit de premiers secours', 4, 10000.00),
    (2, 'Couvertures isothermes', 4, 8000.00),
    (2, 'Lampes solaires', 4, 45000.00),
    (3, 'Fonds d urgence', 3, NULL);

-- Valeurs par défaut pour `etat`
INSERT INTO BNGRC_etat (nom) VALUES
    ('insatisfait'),
    ('satisfait');

-- Vue présentant les sinistres sans exposer les identifiants
CREATE VIEW IF NOT EXISTS BNGRC_vue_sinistre AS
SELECT
    v.nom AS ville,
    b.nom AS besoin,
    o.libellee AS libellee,
    s.quantite AS quantite,
    u.nom AS unite,
    s.date AS date,
    e.nom AS etat
FROM BNGRC_sinistre s
JOIN BNGRC_ville v ON s.id_ville = v.id
JOIN BNGRC_objet o ON s.id_objet = o.id
JOIN BNGRC_besoins b ON o.id_besoins = b.id
JOIN BNGRC_unite u ON o.id_unite = u.id
JOIN BNGRC_etat e ON s.id_etat = e.id;

-- Vue présentant les dons avec les noms liés (ville, besoin, unité)
CREATE VIEW IF NOT EXISTS BNGRC_vue_dons AS
SELECT
    d.id,
    v.nom AS ville,
    b.nom AS besoin,
    o.libellee AS libellee,
    d.quantite AS quantite,
    u.nom AS unite,
    d.date AS date
FROM BNGRC_dons d
JOIN BNGRC_ville v ON d.id_ville = v.id
JOIN BNGRC_objet o ON d.id_objet = o.id
JOIN BNGRC_besoins b ON o.id_besoins = b.id
JOIN BNGRC_unite u ON o.id_unite = u.id;


