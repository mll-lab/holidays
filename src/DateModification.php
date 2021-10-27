<?php

declare(strict_types=1);

namespace MLL\Holidays;

use Carbon\Carbon;

class DateModification
{
    /**
     * @param  callable(Carbon): bool  $shouldCount  Should the given date be added?.
     */
    public static function addDays(Carbon $date, int $days, callable $shouldCount): Carbon
    {
        // Make sure we do not mutate the original date
        $copy = $date->clone();

        while ($days > 0) {
            $copy->addDay();
            if ($shouldCount($copy)) {
                $days--;
            }
        }

        return $copy;
    }

    /**
     * @param  callable(Carbon): bool  $shouldCount  Should the given date be subtracted?.
     */
    public static function subDays(Carbon $date, int $days, callable $shouldCount): Carbon
    {
        // Make sure we do not mutate the original date
        $copy = $date->clone();

        while ($days > 0) {
            $copy->subDay();
            if ($shouldCount($copy)) {
                $days--;
            }
        }

        return $copy;
    }
}
