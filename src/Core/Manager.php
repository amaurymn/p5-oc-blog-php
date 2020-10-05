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
    private string $table;

    public function __construct()
    {
        $this->pdo   = (new PDOFactory())->getPDO();
        $this->table = $this->getTableName();
    }

    /**
     * @return string
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
            ->query('SELECT * FROM ' . $this->table)
            ->fetchAll();
    }

    /**
     * @param array $where
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $where = [], array $order = [], int $limit = null, int $offset = null)
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);

        $this->setWhereParams($where, $query, $binds, $key, $value);
        $this->setOrderBy($order, $query);
        $this->setLimitOffset($limit, $offset, $query);

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
     * @return mixed
     */
    public function findOneBy(array $where = [], array $order = [])
    {
        return $this->findBy($where, $order, 0, 1)[0];
    }

    /**
     * @param array $order
     * @param string $query
     */
    private function setOrderBy(array $order, string &$query): void
    {
        if (!empty($order) && in_array(end($order), ['ASC', 'DESC'])) {
            $query .= 'ORDER BY `' . array_key_first($order) . '` ' .  end($order);
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
        $binds = [];

        if (!empty($where)) {
            $query .= "WHERE ";
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

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param string $query
     */
    private function setLimitOffset(?int $limit, ?int $offset, string &$query): void
    {
        if (!empty($limit)) {
            if (!empty($offset)) {
                $query .= sprintf(" LIMIT %d OFFSET %d", $limit, $offset);
            } else {
                $query .= sprintf(" LIMIT %d", $limit);
            }
        }
    }
}

//create(Entity $entity)
//update(Entity $entity)
//delete(Entity $entity)
