<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class User
{

    public static function findByEmail(string $email): array|false
    {
        $conn = Database::connect();
        $statement = $conn->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function emailExists(string $email): bool
    {
        $connection = Database::connect();

        $statement = $connection->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        return (bool) $statement->fetch();
    }

    public static function create(string $nombre, string $email, string $password): void
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)'
        );
        $statement->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $statement->execute();
    }
}
