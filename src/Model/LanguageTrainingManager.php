<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class LanguageTrainingManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'language_training';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Only when inserting new languages
     */
    public function insertLanguageTraining(array $language): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (language_id, training_id) VALUES (:language_id, :training_id)");
        $statement->bindValue('language_id', $language['language_id'], \PDO::PARAM_INT);
        $statement->bindValue('training_id', $language['training_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Only when deleting languages
     */
    public function deleteLanguageTraining(int $languageId, int $trainingId): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " 
        WHERE language_id = :language_id AND training_id = :training_id");
        $statement->bindValue('language_id', $languageId, \PDO::PARAM_INT);
        $statement->bindValue('training_id', $trainingId, \PDO::PARAM_INT);
        $statement->execute();
    }

    // Since it is an intermediate table, there is no need to create
    // the select or update queries to this point.
}
