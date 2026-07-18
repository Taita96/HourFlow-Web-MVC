<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use Throwable;

class Schedule
{
    public static function allByUser(int $userId): array
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT h.id, h.nombre, h.tienda_id, t.nombre AS tienda_nombre
         FROM horarios h
         JOIN tiendas t ON t.id = h.tienda_id
         WHERE h.usuario_id = :usuario_id
         ORDER BY t.nombre, h.nombre'
        );
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function find(int $id, int $userId): array|false
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT * FROM horarios WHERE id = :id AND usuario_id = :usuario_id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $schedule = $statement->fetch();

        if (!$schedule) {
            return false;
        }

        $blocksStatement = $connection->prepare(
            'SELECT hora_inicio, hora_fin FROM horario_bloques WHERE horario_id = :horario_id ORDER BY orden'
        );
        $blocksStatement->bindValue(':horario_id', $id, PDO::PARAM_INT);
        $blocksStatement->execute();

        $schedule['bloques'] = $blocksStatement->fetchAll();

        return $schedule;
    }

    public static function blocksFor(int $scheduleId, int $userId): array
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT hb.hora_inicio, hb.hora_fin
         FROM horario_bloques hb
         JOIN horarios h ON h.id = hb.horario_id
         WHERE hb.horario_id = :horario_id AND h.usuario_id = :usuario_id
         ORDER BY hb.orden'
        );
        $statement->bindValue(':horario_id', $scheduleId, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function create(int $userId, int $tiendaId, string $name, array $blocks): int
    {
        $connection = Database::connect();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'INSERT INTO horarios (usuario_id, tienda_id, nombre) VALUES (:usuario_id, :tienda_id, :nombre)'
            );
            $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
            $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
            $statement->bindValue(':nombre', $name, PDO::PARAM_STR);
            $statement->execute();

            $scheduleId = (int) $connection->lastInsertId();

            self::insertBlocks($connection, $scheduleId, $blocks);

            $connection->commit();

            return $scheduleId;
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, int $userId, int $tiendaId, string $name, array $blocks): void
    {
        $connection = Database::connect();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'UPDATE horarios SET nombre = :nombre, tienda_id = :tienda_id WHERE id = :id AND usuario_id = :usuario_id'
            );
            $statement->bindValue(':nombre', $name, PDO::PARAM_STR);
            $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
            $statement->execute();

            $deleteStatement = $connection->prepare('DELETE FROM horario_bloques WHERE horario_id = :horario_id');
            $deleteStatement->bindValue(':horario_id', $id, PDO::PARAM_INT);
            $deleteStatement->execute();

            self::insertBlocks($connection, $id, $blocks);

            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function delete(int $id, int $userId): void
    {
        $connection = Database::connect();

        $statement = $connection->prepare('DELETE FROM horarios WHERE id = :id AND usuario_id = :usuario_id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    private static function insertBlocks(PDO $connection, int $scheduleId, array $blocks): void
    {
        $blockStatement = $connection->prepare(
            'INSERT INTO horario_bloques (horario_id, hora_inicio, hora_fin, orden) VALUES (:horario_id, :hora_inicio, :hora_fin, :orden)'
        );

        foreach ($blocks as $index => $block) {
            $blockStatement->bindValue(':horario_id', $scheduleId, PDO::PARAM_INT);
            $blockStatement->bindValue(':hora_inicio', $block['inicio'], PDO::PARAM_STR);
            $blockStatement->bindValue(':hora_fin', $block['fin'], PDO::PARAM_STR);
            $blockStatement->bindValue(':orden', $index + 1, PDO::PARAM_INT);
            $blockStatement->execute();
        }
    }
}
