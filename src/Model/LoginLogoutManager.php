<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
class LoginLogoutManager extends AbstractManager
{
    protected PDO $pdo;

    public const TABLE = 'user';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get one row from database by email and password to login the user.
     */
    public function selectOneUser(string $email, string $password): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE email=:email AND pswd=:password");
        $statement->bindValue('email', $email, \PDO::PARAM_STR);
        $statement->bindValue('password', $password, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
