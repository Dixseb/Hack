<?php

namespace App\Model;

class StackManager extends AbstractManager
{
    public const TABLE = 'stack';

    /**
     * Insert new stack in database
     */
    public function insertStack(array $stack): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`) VALUES (:name)");
        $statement->bindValue('name', $stack['name'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update stack in database
     */
    public function updateStack(array $stack): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name WHERE id=:id");
        $statement->bindValue('id', $stack['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $stack['name'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
