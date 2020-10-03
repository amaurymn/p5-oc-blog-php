<?php

namespace App\Core;

use App\Model\ManagerInterface;
use PDO;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class Manager
{
    private PDO $pdo;
    protected string $table;
    private array $config;

    public function __construct()
    {
        $this->pdo    = (new PDOFactory())->getPDO();
        $this->config = Yaml::parseFile(CONF_DIR . '/entities.yml');
        $this->table = $this->getTableName();
    }

    /**
     * @param string $entity
     * @return $this
     * @throws ReflectionException
     */
    public function getTableName()
    {
        $managerInstance = (new ReflectionClass($this))->getShortName();

        return strtolower(str_replace('Manager', '', $managerInstance));
    }


    /**
     * @return array
     */
    public function findAll()
    {
        return $this->pdo
            ->query('SELECT * FROM '.$this->tableName)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $where
     * @param array $order
     * @param array $limit
     * @return array
     */
    public function findBy(array $where = [], array $order = [], array $limit = [])//add offset
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);

        $this->setWhereParams($where, $query, $binds, $key, $value);
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
     * @param array $where
     * @param array $order
     * @return array
     */
    public function findOneBy(array $where = [], array $order = [])
    {
        return $this->findBy($where, $order, [0,1])[0];
    }

    /**
     * @param array $order
     * @param string $query
     */
    private function setOrderBy(array $order, string &$query): void
    {
        //Only one order but you need to verify that value are only asc or desc
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
     * @param array $limit
     * @param string $query
     */
    private function setLimit(array $limit, string &$query): void
    {
        if (!empty($limit)) {
            $query .= sprintf(" LIMIT %d, %d", $limit[0], $limit[1]);
        }
    }

    /**
     * @param array $where
     * @param string $query
     * @param $binds
     * @param $key
     * @param $value
     */
    private function setWhereParams(array $where, string &$query, &$binds, &$key, &$value): void
    {
        if (!empty($where)) {
            $query .= "WHERE ";

            $binds = [];

            $i = 0;

            foreach ($where as $key => $value) {
                if ($i > 0) {
                    $query .= ' AND ';
                }
                $query .= '`' . $key . '` = ? ';
                $i++;

                $binds[$i] = $value;
            }
        }
    }

    //create(Entity $entity)
    //update(Entity $entity)
    //delete(Entity $entity)
}
