# Créez votre premier blog en PHP

![shield-php-version](https://img.shields.io/badge/PHP%20Version-%5E7.4-007bff)  ![shield-code-size](https://img.shields.io/github/repo-size/amaurymn/p5-oc-blog-php)  [![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=amaurymn_p5-oc-blog-php&metric=ncloc)](https://sonarcloud.io/dashboard?id=amaurymn_p5-oc-blog-php)  [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=amaurymn_p5-oc-blog-php&metric=alert_status)](https://sonarcloud.io/dashboard?id=amaurymn_p5-oc-blog-php)
-----
## **Procédure d'installation:**

### **Pré-requis:**
* **HTTPd:** Apache 2.4+ avec `mod_rewrite` activé.
* **PHP:** PHP 7.4+
* **Database:** MySQL / MariaDB
* **Outils:** 
  * [Composer](https://getcomposer.org/)
  * [NPM](https://www.npmjs.com/get-npm)
    
-----

### **Initialisation du projet:**

1. Cloner le repository git.
2. Dans le dossier cloné, depuis un terminal lancer:
   * `composer install`
   * `npm install`
   * `npm run build`
3. Importer la base de données `DBOCP5BLOG.sql`
4. Copier `config/config.yml.exemple` vers `config/config.yml` et éditer les lignes nécessaires.
5. Copier `config/db-config.yml.exemple` vers `config/db-config.yml` et éditer les lignes nécessaires.
