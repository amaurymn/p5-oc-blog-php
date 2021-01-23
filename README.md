# Créez votre premier blog en PHP

![shield-php-version](https://img.shields.io/badge/PHP%20Version-%5E7.4-007bff)  ![shield-code-size](https://img.shields.io/github/repo-size/amaurymn/p5-oc-blog-php) [![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=amaurymn_p5-oc-blog-php&metric=ncloc)](https://sonarcloud.io/dashboard?id=amaurymn_p5-oc-blog-php)  [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=amaurymn_p5-oc-blog-php&metric=alert_status)](https://sonarcloud.io/dashboard?id=amaurymn_p5-oc-blog-php) [![Maintainability](https://api.codeclimate.com/v1/badges/20982bf891c43b706e6a/maintainability)](https://codeclimate.com/github/amaurymn/p5-oc-blog-php/maintainability)
-----

## **Procédure d'installation:**

### **Pré-requis:**

* **HTTPd:** Apache 2.4+ avec `mod_rewrite` activé.
* **PHP:** PHP 7.4+
* **Database:** MySQL / MariaDB
* **Outils:**
    * [Composer](https://getcomposer.org/)
    * [NPM](https://www.npmjs.com/get-npm)

### **Initialisation du projet:**

1. Cloner le repository git.
2. Dans le dossier cloné, depuis un terminal, lancer:
    1. `composer install`
    2. `npm install`
    3. `npm run build`

3. Importer le schema de base de données `dbocp5blog_dev.sql`
4. Copier `config/config.yml.example` vers `config/config.yml` et éditer les lignes nécessaires.
    1. `install_state` est à false par défaut, ce qui signifie que le site n'est pas encore installé, il passera à true une fois l'administrateur crée.
    2. `env`: options `dev|prod`  (mettre prod une fois le site en ligne)
    3. `mailer`: à remplir avec mes informations smtp du serveur mail pour pouvoir envoyer les mails depuis le site.
5. Copier `config/db-config.yml.example` vers `config/db-config.yml` et éditer les lignes nécessaires correspondante à la base de données MySQL/MariaDB.
6. Lancer le site.
