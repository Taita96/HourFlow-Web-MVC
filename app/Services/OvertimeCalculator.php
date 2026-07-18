<?php

declare(strict_types=1);

namespace App\Services;

class OvertimeCalculator
{
    private const MINUTES_PER_DAY = 1440;

    public static function totalsForRecords(array $records): array
    {
        $workedMinutes = array_sum(array_column($records, 'worked_minutes'));
        $plannedMinutes = array_sum(array_column($records, 'planned_minutes'));

        return [
            'worked_label' => self::format($workedMinutes),
            'planned_label' => self::format($plannedMinutes),
            'extra_label' => self::format(abs($workedMinutes - $plannedMinutes)),
            'extra_positive' => $workedMinutes >= $plannedMinutes,
        ];
    }

    public static function minutesFromBlocks(array $blocks): int
    {
        $minutes = 0;

        foreach ($blocks as $block) {
            $start = self::toMinutes($block['hora_inicio']);
            $end = self::toMinutes($block['hora_fin']);

            // Si la hora de fin es "menor" que la de inicio, asumimos que el
            // turno cruzó la medianoche (ej. 20:00 -> 02:00) y le sumamos un
            // día completo de minutos antes de restar.
            $minutes += $end >= $start ? $end - $start : ($end + self::MINUTES_PER_DAY - $start);
        }

        return $minutes;
    }

    public static function summarize(array $plannedBlocks, array $workedBlocks): array
    {
        $plannedMinutes = self::minutesFromBlocks($plannedBlocks);
        $workedMinutes = self::minutesFromBlocks($workedBlocks);

        return [
            'planned_minutes' => $plannedMinutes,
            'worked_minutes' => $workedMinutes,
            'extra_minutes' => $workedMinutes - $plannedMinutes,
        ];
    }

    public static function format(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        return sprintf('%dh %02dm', $hours, $remaining);
    }

    /**
     * Convierte un array de bloques (con hora_inicio/hora_fin) en un texto
     * legible como "12:00 a 18:00" o "08:00 a 12:00 y 14:00 a 18:00" (turno partido).
     */
    public static function formatBlocks(array $blocks): string
    {
        if (empty($blocks)) {
            return '—';
        }

        $parts = array_map(static function (array $block): string {
            return substr($block['hora_inicio'], 0, 5) . ' a ' . substr($block['hora_fin'], 0, 5);
        }, $blocks);

        return implode(' y ', $parts);
    }

    /**
     * Convierte los arrays paralelos hora_inicio[]/hora_fin[] del formulario
     * en bloques válidos, permitiendo turnos que cruzan la medianoche.
     */
    public static function parseBlocks(array $starts, array $ends): array
    {
        $errors = [];
        $blocks = [];

        foreach ($starts as $index => $start) {
            $end = $ends[$index] ?? '';

            if ($start === '' || $end === '') {
                continue;
            }

            if ($end === $start) {
                $errors[] = 'El bloque ' . ($index + 1) . ' no puede tener la misma hora de inicio y fin.';
                continue;
            }

            $blocks[] = ['inicio' => $start, 'fin' => $end];
        }

        return [$errors, $blocks];
    }

    private static function toMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return ($hours * 60) + $minutes;
    }
}
