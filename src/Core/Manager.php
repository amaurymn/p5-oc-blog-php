<?php

namespace App\Core;

use App\Model\ManagerInterface;
use PDO;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class Manager implements ManagerInterface
{
    private PDO $pdo;
    protected string $table;
    private array $config;

    public function __construct()
    {
        $this->pdo    = (new PDOFactory())->getPDO();
        $this->config = Yaml::parseFile(CONF_DIR . '/entities.yml');
    }

    /**
     * @param string $entity
     * @return $this
     * @throws ReflectionException
     */
    public function getManagerFor(string $entity)
    {
        $shortName = (new ReflectionClass($entity))->getShortName();
        $this->setTable(lcfirst($shortName));

        if (!array_key_exists($shortName, $this->config)) {
            return $this;
        }

        $manager = new $this->config[$shortName]['manager'];
        $manager->setTable($this->table);

        return $manager;
    }

    /**
     * @param array $order
     * @param array $limit
     * @return array
     */
    public function findAll(array $order = [], array $limit = [])
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);
        $this->setOrderBy($order, $query);
        $this->setLimit($limit, $query);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $params
     * @param array $order
     * @param array $limit
     * @return array
     */
    public function findBy(array $params = [], array $order = [], array $limit = [])
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);

        $this->setWhereParams($params, $query, $binds, $key, $value);
        $this->setOrderBy($order, $query);
        $this->setLimit($limit, $query);

        $stmt = $this->pdo->prepare($query);

        foreach ($binds as $key => $value) {
            if (is_int($value) || is_bool($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } elseif (is_a($value, 'DateTime')) {
                $stmt->bindValue($key, $value->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $order
     * @param string $query
     */
    private function setOrderBy(array $order, string &$query): void
    {
        if (!empty($order)) {
            $query .= "ORDER BY ";

            $i = 0;
            foreach ($order as $key => $value) {
                if ($i > 0) {
                    $query .= ', ';
                }
                $query .= '`' . $key . '` ' . $value;
                $i++;
            }
        }
    }

    /**
     * @param array $params
     * @param string $query
     * @param $binds
     * @param $key
     * @param $value
     */
    private function setWhereParams(array $params, string &$query, &$binds, &$key, &$value): void
    {
        if (!empty($params)) {
            $query .= "WHERE ";

            $binds = [];

            $i = 0;

            foreach ($params as $key => $value) {
                if ($i > 0) {
                    $query .= ' AND ';
                }
                $query .= '`' . $key . '` = ? ';
                $i++;

                $binds[$i] = $value;
            }
        }
    }

    /**
     * @param string $tableName
     */
    public function setTable(string $tableName)
    {
        $this->table = $tableName;
    }

    /**
     * @param array $limit
     * @param string $query
     */
    private function setLimit(array $limit, string &$query): void
    {
        if (!empty($limit)) {
            $query .= sprintf(" LIMIT %d, %d", $limit[0], $limit[1]);
        }
    }
}