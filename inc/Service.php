<?php

class Service
{
    public static function formatTime($minutes): string
    {
        $hours = intdiv($minutes, 60);
        $minutes = $minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . "ч " . $minutes . "м.";
        } elseif ($hours > 0) {
            return $hours . "ч.";
        } else {
            return $minutes . "м.";
        }
    }

    public static function formatReportData($data): array
    {
        $exploded = explode("\n", $data);

        unset($exploded[0]);

        $formatted = [];
        $totalMinutes = 0;
        foreach ($exploded as $item) {

            $split = explode('","', $item);

            if (empty($split)) {
                continue;
            }

            $project = $split[1];
            $project = str_replace('-', '', $project);
            $project = preg_replace('/[0-9]+/', '', $project);

            $project = self::getProjectName($project);

            if (count($split) == 10) {
                $minutes = $split[8];
            } elseif (count($split) == 7) {
                $minutes = (int) filter_var($split[5], FILTER_SANITIZE_NUMBER_INT);
            } else {
                $minutes = $split[7];
            }

            $totalMinutes += (int)$minutes;

            $time = self::formatTime($minutes);

            $task = $split[2] . (!empty($split[6] && strpos($split[6], '0') !== 0) ? (' - ' . $split[6]) : '');
            $task = str_replace('"', '', $task);

            $formatted[$project][] = [
                'task' => $task,
                'time' => $time,
            ];
        }

        return [
            'totalTime' => self::formatTime($totalMinutes),
            'structure' => $formatted
        ];
    }

    public static function getProjectName($name) {
        switch ($name) {
            case "TCC": return "Tilda CC";
            case "SRCH": return "Tilda Search";
            case "DOC": return "Tilda Docs";
            case "CRM": return "Tilda CRM";
            case "TILDA": return "Tilda";
            case "TIME": return "Другое";
            case "TP": return "Tilda CC Page";
        }
    }
}

