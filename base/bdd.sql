DROP DATABASE IF EXISTS BNGRC;
CREATE DATABASE BNGRC;
USE BNGRC;

-- ========================
-- TABLES DE REFERENCE
-- ========================

CREATE TABLE BNGRC_besoins(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE BNGRC_unite(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE BNGRC_ville(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE BNGRC_etat(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

-- ========================
-- OBJETS
-- ========================

CREATE TABLE BNGRC_objet(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_besoins INT NOT NULL,
    libellee VARCHAR(255) NOT NULL,
    id_unite INT NOT NULL,
    prix_unitaire DECIMAL(12,2) DEFAULT NULL,
    FOREIGN KEY (id_besoins) REFERENCES BNGRC_besoins(id),
    FOREIGN KEY (id_unite) REFERENCES BNGRC_unite(id)
);

-- ========================
-- SINISTRES (BESOINS)
-- ========================

CREATE TABLE BNGRC_sinistre(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    id_ville INT NOT NULL,
    quantite INT NOT NULL,
    quantite_initiale INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_etat INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id),
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_etat) REFERENCES BNGRC_etat(id)
);

-- ========================
-- DONS
-- ========================

CREATE TABLE BNGRC_dons(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    id_objet INT NOT NULL,
    quantite INT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id)
);

-- ========================
-- ACHATS
-- ========================

CREATE TABLE BNGRC_achat(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    montant_total DECIMAL(14,2) NOT NULL DEFAULT 0,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_objet) REFERENCES BNGRC_objet(id)
);

-- ========================
-- DONNEES DE BASE
-- ========================

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

INSERT INTO BNGRC_etat (nom) VALUES
('insatisfait'),
('satisfait');

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

-- ========================
-- VUES
-- ========================

DROP VIEW IF EXISTS BNGRC_vue_sinistre;
CREATE VIEW BNGRC_vue_sinistre AS
SELECT
    v.nom AS ville,
    b.nom AS besoin,
    o.libellee AS objet,
    s.quantite,
    s.quantite_initiale,
    u.nom AS unite,
    s.date,
    e.nom AS etat
FROM BNGRC_sinistre s
JOIN BNGRC_ville v ON s.id_ville = v.id
JOIN BNGRC_objet o ON s.id_objet = o.id
JOIN BNGRC_besoins b ON o.id_besoins = b.id
JOIN BNGRC_unite u ON o.id_unite = u.id
JOIN BNGRC_etat e ON s.id_etat = e.id;

DROP VIEW IF EXISTS BNGRC_vue_dons;
CREATE VIEW BNGRC_vue_dons AS
SELECT
    d.id,
    v.nom AS ville,
    b.nom AS besoin,
    o.libellee AS objet,
    d.quantite,
    u.nom AS unite,
    d.date
FROM BNGRC_dons d
JOIN BNGRC_ville v ON d.id_ville = v.id
JOIN BNGRC_objet o ON d.id_objet = o.id
JOIN BNGRC_besoins b ON o.id_besoins = b.id
JOIN BNGRC_unite u ON o.id_unite = u.id;
