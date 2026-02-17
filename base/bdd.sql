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
    ordre INT NOT NULL DEFAULT 0,
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
-- VUES
-- ========================

DROP VIEW IF EXISTS BNGRC_vue_sinistre;
CREATE VIEW BNGRC_vue_sinistre AS
SELECT
    v.nom AS ville,
    b.nom AS besoin,
    o.libellee AS objet,
    s.quantite,
    s.ordre,
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
-- ========================
-- DONNEES DE BASE
-- ========================

INSERT INTO BNGRC_besoins (nom) VALUES
('nature'),
('Matériaux'),
('Argent');

INSERT INTO BNGRC_unite (nom) VALUES
('L'),
('Kg'),
('Ariary'),
('unite');

INSERT INTO BNGRC_ville (nom) VALUES
('Toamasina'),
('Mananjary'),
('Nosy Be'),
('Morondava'),
('Farafangana');

INSERT INTO BNGRC_etat (nom) VALUES
('insatisfait'),
('satisfait');

INSERT INTO BNGRC_objet (id_besoins, libellee, id_unite, prix_unitaire) VALUES
(1, 'Riz', 2, 3000.00),
(1, 'Eau', 1, 1000.00),
(2, 'Tôle', 4, 25000.00),
(2, 'Bâche', 4, 15000.00),
(3, 'Argent', 3, 1.00),
(1, 'Huile', 1, 6000.00),
(2, 'Clous', 2, 8000.00),
(2, 'Bois', 4, 10000.00),
(1, 'Haricots', 4, 4000.00),
(2, 'groupe', 4, 6750000.00);

INSERT INTO BNGRC_sinistre 
(id_objet, id_ville, quantite, ordre, quantite_initiale, date, id_etat)
VALUES
-- Toamasina
(1, 1, 800, 17, 800, '2026-02-16', 1),
(2, 1, 1500, 4, 1500, '2026-02-15', 1),
(3, 1, 120, 23, 120, '2026-02-16', 1),
(4, 1, 200, 1, 200, '2026-02-15', 1),
(5, 1, 12000000, 12, 12000000, '2026-02-16', 1),
(10, 1, 3, 16, 3, '2026-02-15', 1),

-- Mananjary
(1, 2, 500, 9, 500, '2026-02-15', 1),
(6, 2, 120, 25, 120, '2026-02-16', 1),
(3, 2, 80, 6, 80, '2026-02-15', 1),
(7, 2, 60, 19, 60, '2026-02-16', 1),
(5, 2, 6000000, 3, 6000000, '2026-02-15', 1),

-- Farafangana
(1, 5, 600, 21, 600, '2026-02-16', 1),
(2, 5, 1000, 14, 1000, '2026-02-15', 1),
(4, 5, 150, 8, 150, '2026-02-16', 1),
(8, 5, 100, 26, 100, '2026-02-15', 1),
(5, 5, 8000000, 10, 8000000, '2026-02-16', 1),

-- Nosy Be
(1, 3, 300, 5, 300, '2026-02-15', 1),
(9, 3, 200, 18, 200, '2026-02-16', 1),
(3, 3, 40, 2, 40, '2026-02-15', 1),
(7, 3, 30, 24, 30, '2026-02-16', 1),
(5, 3, 4000000, 7, 4000000, '2026-02-15', 1),

-- Morondava
(1, 4, 700, 11, 700, '2026-02-16', 1),
(2, 4, 1200, 20, 1200, '2026-02-15', 1),
(4, 4, 180, 15, 180, '2026-02-16', 1),
(8, 4, 150, 22, 150, '2026-02-15', 1),
(5, 4, 10000000, 13, 10000000, '2026-02-16', 1);

INSERT INTO BNGRC_dons
(id_ville, id_objet, quantite, date)
VALUES
-- Argent
(1, 5, 5000000, '2026-02-16'),
(1, 5, 3000000, '2026-02-16'),
(1, 5, 4000000, '2026-02-17'),
(1, 5, 1500000, '2026-02-17'),
(1, 5, 6000000, '2026-02-17'),
(1, 5, 20000000, '2026-02-19'),

-- Nature
(1, 1, 400, '2026-02-16'),
(1, 2, 600, '2026-02-16'),
(1, 9, 100, '2026-02-17'),
(1, 1, 2000, '2026-02-18'),
(1, 2, 5000, '2026-02-18'),
(1, 9, 88, '2026-02-17'),

-- Matériel
(1, 3, 50, '2026-02-17'),
(1, 4, 70, '2026-02-17'),
(1, 3, 300, '2026-02-18'),
(1, 4, 500, '2026-02-19');