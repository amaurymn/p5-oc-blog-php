<?php

namespace App\Core;

use App\Exception\EntityNotFoundException;
use PDO;
use ReflectionClass;
use ReflectionException;

abstract class Manager
{
    protected string $table;
    protected string $entity;
    protected \PDO $pdo;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->pdo    = (new PDOFactory())->getPDO();
        $this->table  = $this->getTableName();
        $this->entity = "App\Entity\\" . ucfirst($this->table);
    }

    /**
     * make request to find all data with optional parameters
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll(array $order = [], int $limit = null, int $offset = null): array
    {
        $query = sprintf("SELECT * FROM %s ", $this->table);
        $this->setOrderBy($order, $query);
        $this->setLimitOffset($limit, $offset, $query);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $entityResults = [];

        foreach($results as $result){
            $entityResults[] = new $this->entity($result);
        }

        return $entityResults;
    }

    /**
     * make request to find all data with optional parameters
     * @param array $where
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array|mixed
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

        if ($stmt->rowCount() > 1) {
            foreach ($stmt->fetchAll() as $result) {
                $entityResults[] = new $this->entity($result);
            }
        } else {
            $entityResults = $stmt->fetch();
        }

        return $entityResults;
    }

    /**
     * make request to find one specified data with optional parameters
     * @param array $where
     * @param array $order
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function findOneBy(array $where = [], array $order = [])
    {
        $result = $this->findBy($where, $order, 0, 1);

        if (!$result) {
            throw new EntityNotFoundException("Le contenu n'existe pas.");
        }

        return new $this->entity($this->findBy($where, $order, 0, 1));
    }

    /**
     * make request to add data to the bdd
     * @param Entity $entity
     * @throws ReflectionException
     */
    public function create(Entity $entity): void
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
     * make request to update data from the bdd
     * @param Entity $entity
     * @throws ReflectionException
     */
    public function update(Entity $entity): void
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
     * make request to delete data from the bdd
     * @param Entity $entity
     */
    public function delete(Entity $entity): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $stmt->bindValue(':id', $entity->getId());
        $stmt->execute();
    }

    /**
     * make request to get dashboard stats
     * @return mixed
     */
    public function getDashboardStats()
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM
                (SELECT COUNT(id) AS art_online FROM article) AS art,
                (SELECT COUNT(id) AS com_total FROM comment) AS cv,
                (SELECT COUNT(id) AS com_pending FROM comment WHERE online = 0) AS cp,
                (SELECT COUNT(id) AS usr_registered FROM user) AS usr;
        ");

        return $stmt->fetch();
    }

    /**
     * return the working table name
     * @return string
     */
    private function getTableName(): string
    {
        $managerInstance = (new ReflectionClass($this))->getShortName();

        return strtolower(str_replace('Manager', '', $managerInstance));
    }

    /**
     * return string order by
     * @param array $order
     * @param string $query
     */
    private function setOrderBy(array $order, string &$query): void
    {
        if (!empty($order) && in_array(end($order), ['ASC', 'DESC'])) {
            $query .= 'ORDER BY `' . array_key_first($order) . '` ' . end($order);
        }
    }

    /**
     * return string with where, and, params
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
            $i     = 0;

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
     * return string with offset
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
     * return string with the insert statement
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
     * return string with update statement
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
     * set binding depending to type
     * @param $binds
     * @param \PDOStatement $stmt
     */
    private function setBinding($binds, \PDOStatement $stmt): void
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
        $columns    = [];
        $properties = $entity->getObjectProperties();
        foreach ($properties as $property) {
            $columns[] = $this->camelCaseToSnakeCase($property->name);
        }

        return $columns;
    }

    /**
     * format camelCase to snake_case
     * @param string $property
     * @return string
     */
    private function camelCaseToSnakeCase(string $property): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
    }

    /**
     * return entity bindings
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
