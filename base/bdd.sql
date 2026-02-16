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

-- Quelques sinistres exemples (libellés courts et réalistes)
INSERT INTO sinistre (id_besoins, libellee, id_ville, quantite, id_unite) VALUES
    (1, 'huile', 1, 1, 1),
    (2, 'clous', 2, 50, 4),
    (3, 'aide financière urgence', 3, 500000, 3);