<?php

namespace App\Core;

use PDO;
use ReflectionClass;
use ReflectionException;

abstract class Manager
{
    private PDO $pdo;
    private string $table;
    private string $entity;

    public function __construct()
    {
        $this->pdo    = (new PDOFactory())->getPDO();
        $this->table  = $this->getTableName();
        $this->entity = "App\Entity\\" . strtoupper($this->table);
    }

    /**
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll(array $order = [], int $limit = null, int $offset = null)
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);
        $this->setOrderBy($order, $query);
        $this->setLimitOffset($limit, $offset, $query);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $entityResults=[];

        foreach($results as $result){
            array_push($entityResults, new $this->entity($result));
        }

        return $entityResults;
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
        $this->setBinding($binds, $stmt);
        $stmt->execute();

        $entityResults = [];

        if ($stmt->rowCount() > 1) {
            array_push($entityResults, new $this->entity($stmt->fetchAll()));
        } else {
            $entityResults = $stmt->fetch();
        }

        return $entityResults;
    }

    /**
     * @param array $where
     * @param array $order
     * @return mixed
     */
    public function findOneBy(array $where = [], array $order = [])
    {
        return new $this->entity($this->findBy($where, $order, 0, 1));
    }

    /**
     * @param Entity $entity
     * @throws ReflectionException
     */
    public function create(Entity $entity)
    {
        $vars[] = array_values($this->getColumns($entity));

        $query = 'INSERT INTO ' . $this->table . $this->makeInsertQuery($vars[0]);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        $binds = $this->bindFieldsToEntity($vars, $entity);

        $stmt = $this->pdo->prepare($query);
        $this->setBinding($binds, $stmt);
        $stmt->execute();
    }

    /**
     * @param Entity $entity
     * @throws ReflectionException
     */
    public function update(Entity $entity)
    {
        $vars[] = array_values($this->getColumns($entity));

        $query = "UPDATE " . $this->table . $this->makeUpdateQuery($vars[0]) . " WHERE id = :id";
        $entity->setUpdatedAt(new \DateTime());

        $binds = $this->bindFieldsToEntity($vars, $entity);

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $entity->getId());
        $this->setBinding($binds, $stmt);
        $stmt->execute();
    }

    /**
     * @param Entity $entity
     */
    public function delete(Entity $entity)
    {
        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $stmt->bindValue(':id', $entity->getId());
        $stmt->execute();
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function getTableName()
    {
        $managerInstance = (new ReflectionClass($this))->getShortName();

        return strtolower(str_replace('Manager', '', $managerInstance));
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

    /**
     * @param array $vars
     * @return string
     */
    private function makeInsertQuery(array $vars): string
    {
        $columns = [];
        $values  = [];
        foreach ($vars as $column) {
            if ($column !== 'id') {
                $columns[] = $this->table . '.' . $column;
                $values[]  = ':' . $column;
            }
        }

        return ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';
    }

    /**
     * @param array $vars
     * @return string
     */
    private function makeUpdateQuery(array $vars): string
    {
        $values = [];

        foreach ($vars as $column) {
            if ($column !== 'id') {
                $values[] = $this->table . '.' . $column . ' = :' . $column;
            }
        }

        return " SET " . implode(', ', $values);
    }

    /**
     * @param $binds
     * @param \PDOStatement $stmt
     */
    private function setBinding($binds, \PDOStatement $stmt)
    {
        foreach ($binds as $key => $value) {
            if (is_int($value) || is_bool($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } elseif (is_a($value, 'DateTime')) {
                $stmt->bindValue($key, $value->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
    }

    /**
     * @param Entity $entity
     * @return array
     * @throws ReflectionException
     */
    private function getColumns(Entity $entity): array
    {
        $columns = [];
        $properties = $entity->getObjectProperties();
        foreach ($properties as $property) {
            $columns[] = $this->camelCaseToSnakeCase($property->name);
        }

        return $columns;
    }

    /**
     * @param string $property
     * @return string
     */
    private function camelCaseToSnakeCase(string $property): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
    }

    /**
     * @param $vars
     * @param Entity $entity
     * @return array
     */
    private function bindFieldsToEntity($vars, Entity $entity): array
    {
        $binds = [];
        foreach ($vars[0] as $field) {
            $method        = 'get' . ucfirst($entity->snakeCaseToCamelCase($field));
            $binds[$field] = $entity->$method();
        }

        return $binds;
    }
}
