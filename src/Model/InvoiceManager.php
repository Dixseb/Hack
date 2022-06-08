<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class InvoiceManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'invoice';
    public const TABLE2 = 'invoice_training';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get all row from database.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT i.id, i.created_at, i.user_id, i.total, u.firstname, u.lastname  
            FROM ' . static::TABLE . ' AS i
            INNER JOIN user AS u ON u.id = i.user_id';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get one row from database by ID.
     */
    public function selectOneById(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT i.id, i.created_at, i.user_id, i.total, u.firstname, u.lastname  
            FROM " . static::TABLE . " AS i 
            INNER JOIN user AS u ON u.id = i.user_id
            WHERE i.id=:id
        ");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Get all invoices for a specific user
     */
    public function selectInvoicesByUserId(int $id, string $orderBy = '', string $direction = 'ASC'): array|false
    {
        // prepared request
        $query = "SELECT i.id, i.created_at, i.user_id, i.total, u.firstname, u.lastname 
            FROM " . static::TABLE . " AS i 
            JOIN user AS u ON u.id = i.user_id
            JOIN invoice_training AS it ON it.invoice_id = i.id
            JOIN training AS tr ON tr.id = it.training_id 
            WHERE i.user_id=:id 
            GROUP by i.id
        ";
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get all trainings title from an invoice id
     */
    public function selectTrainingsInInvoice(int $id, string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT tr.title, tr.price 
            FROM ' . static::TABLE2 . ' AS it
            INNER JOIN training AS tr ON tr.id = it.training_id
            WHERE it.invoice_id=:id
        ';
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Delete all rows from an ID
     * It delete a specific invoice
     */
    public function deleteInvoice(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Delete a specific row from selected IDs
     * It delete a specific training for an invoice
     */
    public function deleteTrainingInInvoice(int $invoiceId, int $trainingId): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE2 . "
        WHERE invoice_id = :invoice.id AND training_id = :training.id");
        $statement->bindValue('user_id', $invoiceId, \PDO::PARAM_INT);
        $statement->bindValue('training_id', $trainingId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Insert new invoices in database
     */
    // public function insert(array $invoice): int
    // {
    //     $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
    //     (user_id, total) VALUES (:user_id, :total)");
    //     $statement->bindValue('user_id', $invoice['userid'], \PDO::PARAM_INT);
    //     $statement->bindValue('total', $invoice['total'], \PDO::PARAM_INT);

    //     $statement->execute();
    //     return (int)$this->pdo->lastInsertId();
    // }

    public function generateInvoice($invoice): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (user_id, total) VALUES (:user_id, :total)");
        $statement->bindValue('user_id', $invoice['user']['id'], \PDO::PARAM_INT);
        $statement->bindValue('total', $invoice['total'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    // Associate the invoice and trainings
    public function insertTrainingInInvoice(array $invoiceTraining): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE2 . " 
            (invoice_id, training_id) VALUES (:invoice_id, :training_id)");
        $statement->bindValue('invoice_id', $invoiceTraining['invoice_id'], \PDO::PARAM_INT);
        $statement->bindValue('training_id', $invoiceTraining['training_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update invoice in database
     */
    public function update(array $invoice): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET user_id = :user_id, total = :total WHERE id=:id
        ");
        $statement->bindValue('id', $invoice['id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $invoice['userid'], \PDO::PARAM_INT);
        $statement->bindValue('total', $invoice['total'], \PDO::PARAM_INT);

        return $statement->execute();
    }
}
