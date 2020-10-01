<?php


namespace App\Core;

class Manager
{
    private $db;

    private $table;

    public function __construct()
    {
        $this->db = PDOFactory::getDBConnexion();
    }

    public function getManagerFor(string $entity)
    {
        //todo: lire yaml, chopper la valeur de entities.Taclass.manager => class_name
        //todo: return new \\class_name
        $this->table = lcfirst((new \ReflectionClass($entity))->getShortName());

        return $this;
    }

    public function findAll() {

    }

    public function findBy(array $params = []) {

    }
}