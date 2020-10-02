<?php

namespace App\Core;

use PDO;
use Symfony\Component\Yaml\Yaml;

class PDOFactory
{
    private array $config;
    private $pdo;

    public function __construct()
    {
        $this->config = Yaml::parseFile(CONF_DIR  . '/db-config.yml');

        $dsn     = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['dbcharset']}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            if ($this->pdo === null) {
                $this->pdo = new PDO($dsn, $this->config['dbuser'], $this->config['dbpswd'], $options);
            }
        } catch (\PDOException $e) {
            echo "ERROR: " . $e->getMessage();
            die();
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}