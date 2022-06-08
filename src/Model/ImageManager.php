<?php

namespace App\Model;

class ImageManager extends AbstractManager
{
    public const TABLE = 'image';

    /**
     * Get all row from image.
     */
    public function selectAllImages(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT image.id as image_id, image.url, training.id as training_id FROM ' . static::TABLE .
        ' INNER JOIN training ON ' . static::TABLE . '.training_id = training.id';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get one row from database by ID.
     */
    public function selectImageById(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT image.id as image_id, image.url, training.id as training_id
        FROM " . static::TABLE .
        " INNER JOIN training ON " . static::TABLE . ".training_id = training.id
        WHERE " . static::TABLE . ".id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Insert new image in database
     */
    public function insertImageWithTrainingId(array $image)
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        " (`url`, `training_id`) VALUES (:url, :training_id)");
        $statement->bindValue('url', $image['url'], \PDO::PARAM_STR);
        $statement->bindValue('training_id', $image['trainingId'], \PDO::PARAM_INT);

        $statement->execute();

        //return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update image in database
     */
    public function updateImage(array $image): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `url` = :url, `training_id` = :training_id
        WHERE id=:id");
        $statement->bindValue('id', $image['id'], \PDO::PARAM_INT);
        $statement->bindValue('url', $image['url'], \PDO::PARAM_STR);
        $statement->bindValue('training_id', $image['training_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }
}
