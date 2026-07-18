<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use Throwable;

class WorkRecord
{
    public static function allByUser(int $userId): array
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT r.id, r.fecha, r.horario_id, h.nombre AS horario_nombre
             FROM registros r
             LEFT JOIN horarios h ON h.id = r.horario_id
             WHERE r.usuario_id = :usuario_id
             ORDER BY r.fecha DESC'
        );
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function allByUserForMonth(int $userId, string $start, string $end, ?int $tiendaId = null): array
    {
        $connection = Database::connect();

        $sql = 'SELECT r.id, r.fecha, r.tienda_id, t.nombre AS tienda_nombre, r.horario_id, h.nombre AS horario_nombre
            FROM registros r
            JOIN tiendas t ON t.id = r.tienda_id
            LEFT JOIN horarios h ON h.id = r.horario_id
            WHERE r.usuario_id = :usuario_id AND r.fecha BETWEEN :start AND :end';

        if ($tiendaId !== null) {
            $sql .= ' AND r.tienda_id = :tienda_id';
        }

        $sql .= ' ORDER BY r.fecha ASC';

        $statement = $connection->prepare($sql);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':start', $start, PDO::PARAM_STR);
        $statement->bindValue(':end', $end, PDO::PARAM_STR);

        if ($tiendaId !== null) {
            $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    public static function find(int $id, int $userId): array|false
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT * FROM registros WHERE id = :id AND usuario_id = :usuario_id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $record = $statement->fetch();

        if (!$record) {
            return false;
        }

        $record['bloques'] = self::blocksFor($id);

        return $record;
    }

    public static function findByDate(string $date, int $userId, int $tiendaId): array|false
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT * FROM registros WHERE fecha = :fecha AND usuario_id = :usuario_id AND tienda_id = :tienda_id LIMIT 1'
        );
        $statement->bindValue(':fecha', $date, PDO::PARAM_STR);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public static function create(int $userId, int $tiendaId, string $date, ?int $scheduleId, array $blocks): int
    {
        $connection = Database::connect();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'INSERT INTO registros (usuario_id, tienda_id, horario_id, fecha) VALUES (:usuario_id, :tienda_id, :horario_id, :fecha)'
            );
            $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
            $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
            $statement->bindValue(':horario_id', $scheduleId, $scheduleId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $statement->bindValue(':fecha', $date, PDO::PARAM_STR);
            $statement->execute();

            $recordId = (int) $connection->lastInsertId();

            self::insertBlocks($connection, $recordId, $blocks);

            $connection->commit();

            return $recordId;
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, int $userId, int $tiendaId, string $date, ?int $scheduleId, array $blocks): void
    {
        $connection = Database::connect();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'UPDATE registros SET fecha = :fecha, tienda_id = :tienda_id, horario_id = :horario_id WHERE id = :id AND usuario_id = :usuario_id'
            );
            $statement->bindValue(':fecha', $date, PDO::PARAM_STR);
            $statement->bindValue(':tienda_id', $tiendaId, PDO::PARAM_INT);
            $statement->bindValue(':horario_id', $scheduleId, $scheduleId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
            $statement->execute();

            $deleteStatement = $connection->prepare('DELETE FROM registro_bloques WHERE registro_id = :registro_id');
            $deleteStatement->bindValue(':registro_id', $id, PDO::PARAM_INT);
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

        $statement = $connection->prepare('DELETE FROM registros WHERE id = :id AND usuario_id = :usuario_id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function blocksFor(int $recordId): array
    {
        $connection = Database::connect();

        $statement = $connection->prepare(
            'SELECT hora_inicio, hora_fin FROM registro_bloques WHERE registro_id = :registro_id ORDER BY orden'
        );
        $statement->bindValue(':registro_id', $recordId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    private static function insertBlocks(PDO $connection, int $recordId, array $blocks): void
    {
        $statement = $connection->prepare(
            'INSERT INTO registro_bloques (registro_id, hora_inicio, hora_fin, orden) VALUES (:registro_id, :hora_inicio, :hora_fin, :orden)'
        );

        foreach ($blocks as $index => $block) {
            $statement->bindValue(':registro_id', $recordId, PDO::PARAM_INT);
            $statement->bindValue(':hora_inicio', $block['inicio'], PDO::PARAM_STR);
            $statement->bindValue(':hora_fin', $block['fin'], PDO::PARAM_STR);
            $statement->bindValue(':orden', $index + 1, PDO::PARAM_INT);
            $statement->execute();
        }
    }
}
