# CONSIGNES PAGES PAR PAGES 

## accueil
Une page d'accueil dédiée à la présentation de l’association : son histoire, ses événements marquants, son but, etc 

## événements du club :
- Présentation détaillée de chaque événement : description, galerie photo **html, js**
- Compétitions et Résultats **php, bdd, java, html, cs**
- Calendrier des rendez-vous : matchs, tournois, sorties, galas : gestion d’un agenda en bdd **java, php, bdd, html, css**

## partenaires
Une page pour mettre en avant les partenaires et les institutions soutenant l’association (en n’oubliant pas, au passage, de procéder à un échange de liens des sites internet avec ces différents soutiens). Il ne faudra pas oublier de positionner clairement les icônes renvoyant les internautes vers les réseaux sociaux de l’association **html, css**

## multimédia 
Un espace multimédia regroupant des galeries photos et vidéos permettant de garder une trace des événements organisés ou auxquels a participé l’association sportive **html, css, js**

## téléchargeables
Un espace d’éléments téléchargeables : dossiers d'inscription/adhésion, programmes, calendriers des rencontres, éléments au format .PDF téléchargeables en 1 clic **php, bdd, html, css**

## membres

### connection
Un espace membre avec connexion sécurisée (et profil personnalisé) **php, bdd, html, css, js**

### inscription
Une page d’inscription où apparaît un formulaire à l’attention des internautes.
- version 1 : envoie de mail à un administrateur **php, bdd, html, css**
- version 2 : ajout directe dans la base de donnée **php, bdd, html, css**

## contact/profil ?
- Un espace permettant aux membres d’envoyer des documents administratifs via le site comme une photo pour la carte d'adhérent ou un certificat médical **php, bdd, html, css , js**

# CODE SQL
## Créer la base de donnée 

```sql
-- Création de la base de données RAPIDOS
CREATE DATABASE RAPIDOS;
USE RAPIDOS;

-- Création de la table MEMBRES
CREATE TABLE MEMBRES (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin BOOLEAN NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    mail VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL 
);

-- Création de la table COMPETITION
CREATE TABLE COMPETITION (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    heure TIME NOT NULL,
    distance INT NOT NULL CHECK (distance > 0),
    sexe VARCHAR(1),
    lieu VARCHAR(100) NOT NULL,
    age INT CHECK (age >= 0),
    nbplace INT CHECK (nbplace >= 0)
);

-- Création de la table INSCRIPTION
CREATE TABLE INSCRIPTION (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_membre INT,
    id_competition INT,
    date_inscription DATE NOT NULL,
    FOREIGN KEY (id_membre) REFERENCES MEMBRES(id),
    FOREIGN KEY (id_competition) REFERENCES COMPETITION(id)
);

-- Création de la table DOCS
CREATE TABLE DOCS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    comm TEXT,
    lien VARCHAR(255) -- Lien vers le document ou fichier
);

-- Création de la table RESULTATS
CREATE TABLE RESULTATS (
    id_competition INT,
    fichier VARCHAR(255);
    PRIMARY KEY (id_competition),
    FOREIGN KEY (id_competition) REFERENCES COMPETITION(id)
);

CREATE TABLE LICENCE (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username INT,
    sexe BOOLEAN NOT NULL,
    mail VARCHAR(100) UNIQUE NOT NULL,
    ville VARCHAR(50) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    date_naiss DATE NOT NULL,
    nationnalite varchar(50) NOT NULL,
    nom_p varchar(50),
    prof_p varchar(50),
    prof_m varchar(50),
    tel_p varchar(20),
    tel_m varchar(20),
    mail_p varchar(100),
    carte_id varchar(255),
    certif_med varchar(255),
    photo_id varchar(255),
    cotisation_m varchar(50),
    cotisation_som INT,
    valide INT,
    FOREIGN KEY (username) REFERENCES MEMBRES(username)
);

CREATE TABLE MEDIA (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    lien VARCHAR(255) NOT NULL,
    date_uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE evenement (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Identifiant unique pour chaque événement
    username VARCHAR(255) NOT NULL,               -- Nom d'utilisateur lié à membres.username
    title VARCHAR(255) NOT NULL,                  -- Titre de l'événement
    description TEXT,                             -- Description de l'événement
    event_date DATE NOT NULL,                     -- Date de l'événement
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date d'ajout, par défaut la date/heure actuelle
    FOREIGN KEY (username) REFERENCES membres(username) -- Clé étrangère vers membres.username
);
```