<?php


namespace App\Core;


use PDO;
use Symfony\Component\Yaml\Yaml;

class PDOFactory
{
    private static $dbConfig;

    public static function getDBConnexion()
    {
        try {
            self::setDbConfig();
        } catch (\Exception $e) {
            die ('[BlogConfigError]: ' . $e);
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $dsn = 'mysql:host=' . self::$dbConfig['host'] . ';port=' . self::$dbConfig['port'] . ';dbname=' . self::$dbConfig['dbname'] . ';charset=' . self::$dbConfig['charset'];
        $db = new PDO($dsn, self::$dbConfig['dbuser'], self::$dbConfig['password'], $options);

        return $db;
    }

    private static function setDbConfig()
    {
        $dbConfig_file = CONF_DIR . '/db-config.yml';


        if (!file_exists($dbConfig_file)) {
            throw new \Exception("Fichier " . $dbConfig_file . " manquant");
        }
        self::$dbConfig = Yaml::parseFile(CONF_DIR . '/db-config.yml');
    }
}