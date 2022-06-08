<?php

namespace App\Model;

class TrainingManager extends AbstractManager
{
    public const TABLE = 'training';
    public const TABLE2 = 'training_user';

    public function groupTrainings(string $stackId, string $orderBy = '', string $direction = 'ASC'): array
    {
        $statement = ("SELECT training.id, training.title, training.price, training.description, 
            training.duration, training.date_start, training.date_end, training.max_students, s.name 
            FROM " . self::TABLE . " 
            JOIN stack AS s ON s.id = training.stack_id
            WHERE training.stack_id = " . $stackId
        );

        // if ($groupBy) {
        //     $statement .= ' GROUP BY ' . $groupBy;
        // }
        if ($orderBy) {
            $statement .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($statement)->fetchAll();
    }
    public function selectAllTrainings(string $orderBy = '', string $direction = 'ASC'): array
    {
        $statement = ("SELECT training.id, training.title, training.price, training.description, 
        training.duration, training.date_start, training.date_end,
         training.max_students, training_user.user_id FROM "
         . self::TABLE . ' LEFT JOIN stack ON ' . self::TABLE . '.stack_id = stack.id
         JOIN training_user ON training.id = training_user.training_id');
        if ($orderBy) {
            $statement .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($statement)->fetchAll();
    }

    public function selectOneById(int $id): array|false
    {
        $statement = $this->pdo->prepare("SELECT training.id, training.title, training.price, training.description,
         training.duration, training.date_start, training.date_end, training.max_students, stack.name,
         COUNT(training_user.user_id) AS participants
        FROM " . self::TABLE . ' LEFT JOIN stack ON ' . self::TABLE . '.stack_id = stack.id 
        LEFT JOIN training_user ON training.id = training_user.training_id
         
        WHERE ' . self::TABLE . '.id=:id');
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function insert(array $training): int
    {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`title`,`price`,`description`,`duration`,
        `date_start`,`date_end`,`max_students`,`stack_id`) VALUES (:title, :price, :description, :duration,
        :date_start, :date_end, :max_students, :stack_id)");
        $statement->bindValue('title', $training['title'], \PDO::PARAM_STR);
        $statement->bindValue('price', $training['price'], \PDO::PARAM_STR);
        $statement->bindValue('description', $training['description'], \PDO::PARAM_STR);
        $statement->bindValue('duration', $training['duration'], \PDO::PARAM_INT);
        $statement->bindValue('date_start', $training['date_start'], \PDO::PARAM_STR);
        $statement->bindValue('date_end', $training['date_end'], \PDO::PARAM_STR);
        $statement->bindValue('max_students', $training['max_students'], \PDO::PARAM_INT);
        $statement->bindValue('stack_id', $training['stack_id'], \PDO::PARAM_INT);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $training): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title,`price` = :price,
        `description` = :description, `duration` = :duration, `date_start` = :date_start,
        `date_end` = :date_end, `max_students` = :max_students WHERE id=:id");
        $statement->bindValue('id', $training['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $training['title'], \PDO::PARAM_STR);
        $statement->bindValue('price', $training['price'], \PDO::PARAM_STR);
        $statement->bindValue('description', $training['description'], \PDO::PARAM_STR);
        $statement->bindValue('duration', $training['duration'], \PDO::PARAM_INT);
        $statement->bindValue('date_start', $training['date_start'], \PDO::PARAM_STR);
        $statement->bindValue('date_end', $training['date_end'], \PDO::PARAM_STR);
        $statement->bindValue('max_students', $training['max_students'], \PDO::PARAM_INT);
        return $statement->execute();
    }

    public function searchByTitle($regex): array
    {
        $statement = ("SELECT * FROM " . self::TABLE . " WHERE title REGEXP '{$regex}'");
        $statement = $this->pdo->prepare($statement);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function countResults($regex): int
    {
        $statement = ("SELECT COUNT(*) FROM " . self::TABLE . " WHERE title REGEXP '{$regex}'");
        $statement = $this->pdo->prepare($statement);
        $statement->execute();
        $count = $statement->fetchColumn();
        return $count;
    }

    /**
     * Insert a new participant
     */
    public function insertParticipant(array $trainingParticipant): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE2 . " 
            (training_id, user_id) VALUES (:training_id, :user_id)");
        $statement->bindValue('training_id', $trainingParticipant['training_id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $trainingParticipant['user_id'], \PDO::PARAM_INT);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function trainingsByFilter(string $filter, string $orderBy = '', string $direction = 'ASC'): array
    {
        $statement = $this->pdo->prepare("SELECT training.id, training.title, training.price, training.description, 
        training.duration, training.date_start, training.date_end, training.max_students, l.id, l.name
        FROM " . "language_training  
        INNER JOIN language AS l ON l.id = language_training.language_id
        INNER JOIN training ON training.id = language_training.training_id
        WHERE l.id LIKE :filter
        ");

        if ($orderBy) {
            $statement .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        $statement->bindValue(':filter', $filter . '%', \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function trainingsByStack(string $filter, string $orderBy = '', string $direction = 'ASC'): array
    {
        $statement = $this->pdo->prepare("SELECT training.id, training.title, training.price, training.description, 
        training.duration, training.date_start, training.date_end, training.max_students, s.id, s.name
        FROM " . self::TABLE . " 
        INNER JOIN stack AS s ON s.id = training.stack_id
        WHERE s.id LIKE :filter
        ");

        if ($orderBy) {
            $statement .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        $statement->bindValue(':filter', $filter . '%', \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
