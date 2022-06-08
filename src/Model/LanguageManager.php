<?php

namespace App\Model;

class LanguageManager extends AbstractManager
{
    public const TABLE = 'language';

    /**
     * Insert new Language in database
     */
    public function insert(array $language): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`) VALUES (:name)");
        $statement->bindValue('name', $language['name'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update Language in database
     */
    public function update(array $language): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name WHERE id=:id");
        $statement->bindValue('id', $language['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $language['name'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
