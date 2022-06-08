<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class InvoiceTrainingManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'invoice_training';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Only when deleting invoices
     */
    public function delete(int $invoiceId): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " 
        INNER JOIN invoice ON invoice.id = invoice_training.invoice_id 
        INNER JOIN training ON training.id = invoice_training.training_id 
        WHERE invoice_training.invoice_id = :invoice.id AND invoice_training.training_id = :training.id");
        $statement->bindValue('invoice_id', $invoiceId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Only when inserting new invoices
     */
    public function insert(array $invoice): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (invoice_id, training_id) VALUES (:invoice_id, :training_id)");
        $statement->bindValue('invoice_id', $invoice['invoice_id'], \PDO::PARAM_INT);
        $statement->bindValue('training_id', $invoice['training_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    // Since it is an intermediate table, there is no need to create
    // the select or update queries to this point.
}
