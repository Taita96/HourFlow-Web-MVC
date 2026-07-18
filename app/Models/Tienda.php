<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Tienda
{
    public static function allByUser(int $userId): array
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT id, nombre FROM tiendas WHERE usuario_id = :usuario_id ORDER BY nombre'
        );
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function find(int $id, int $userId): array|false
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT * FROM tiendas WHERE id = :id AND usuario_id = :usuario_id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public static function create(int $userId, string $name): int
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'INSERT INTO tiendas (usuario_id, nombre) VALUES (:usuario_id, :nombre)'
        );
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':nombre', $name, PDO::PARAM_STR);
        $statement->execute();

        return (int) $connection->lastInsertId();
    }

    public static function update(int $id, int $userId, string $name): void
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'UPDATE tiendas SET nombre = :nombre WHERE id = :id AND usuario_id = :usuario_id'
        );
        $statement->bindValue(':nombre', $name, PDO::PARAM_STR);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function delete(int $id, int $userId): void
    {
        $connection = Database::connect();

        $statement = $connection->prepare('DELETE FROM tiendas WHERE id = :id AND usuario_id = :usuario_id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }
}