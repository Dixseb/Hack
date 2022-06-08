<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

class WishlistManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'wishlist';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT w.id, w.user_id, w.training_id, t.title FROM ' . static::TABLE . ' AS w
            INNER JOIN user AS u ON u.id = w.user_id 
            INNER JOIN training AS t ON t.id = w.training_id';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        return $this->pdo->query($query)->fetchAll();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function insert(array $wishlist): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (user_id, training_id)
        VALUES (:user_id, :training_id)");
        $statement->bindValue('user_id', $wishlist['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('training_id', $wishlist['training_id'], \PDO::PARAM_INT);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
