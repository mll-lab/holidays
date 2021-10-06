<?php

declare(strict_types=1);

namespace MLL\Holidays;

use Carbon\Carbon;

class Holidays
{
    public const HOLIDAYS_STATIC = [
        '0101' => 'Neujahrstag',
        '0601' => 'Heilige Drei Könige',
        '0105' => 'Tag der Arbeit',
        '1508' => 'Maria Himmelfahrt',
        '0310' => 'Tag der Deutschen Einheit',
        '0111' => 'Allerheiligen',
        '2412' => 'Heilig Abend',
        '2512' => 'Erster Weihnachtstag',
        '2612' => 'Zweiter Weihnachtstag',
        '3112' => 'Sylvester',
    ];

    public const SAMSTAG = 'Samstag';
    public const SONNTAG = 'Sonntag';
    public const KARFREITAG = 'Karfreitag';
    public const OSTERSONNTAG = 'Ostersonntag';
    public const OSTERMONTAG = 'Ostermontag';
    public const CHRISTI_HIMMELFAHRT = 'Christi Himmelfahrt';
    public const PFINGSTSONNTAG = 'Pfingstsonntag';
    public const PFINGSTMONTAG = 'Pfingstmontag';
    public const FRONLEICHNAM = 'Fronleichnam';
    public const REFORMATIONSTAG_500_JAHRE_REFORMATION = 'Reformationstag (500 Jahre Reformation)';
    public const BETRIEBSAUSFLUG_2019 = 'Betriebsausflug 2019';

    public const SECONDS_IN_A_DAY = 60 * 60 * 24;

    /**
     * Checks if given date is an MLL working day.
     */
    public static function isMLLWorkingDay(Carbon $date): bool
    {
        return is_null(self::name($date));
    }

    /**
     * Checks if given date is an MLL holiday.
     */
    public static function isMLLHoliday(Carbon $date): bool
    {
        return ! self::isMLLWorkingDay($date);
    }

    /**
     * Returns the name of the holiday if the date happens to land on one.
     */
    public static function name(Carbon $date): ?string
    {
        $holidayMap = self::buildHolidayMap($date);
        $holiday = $holidayMap[self::dayOfTheYear($date)] ?? null;
        if (is_string($holiday)) {
            return $holiday;
        }

        if ($date->isSaturday()) {
            return self::SAMSTAG;
        }

        if ($date->isSunday()) {
            return self::SONNTAG;
        }

        return null;
    }

    public static function addMLLWorkingDays(Carbon $date, int $days): Carbon
    {
        // Make sure we do not mutate the original date
        $copy = $date->clone();

        while ($days > 0) {
            $copy->addDay();
            if (self::isMLLWorkingDay($copy)) {
                $days--;
            }
        }

        return $copy;
    }

    /**
     * Returns a map from day/month to named holidays.
     *
     * @return array<string, string>
     */
    protected static function buildHolidayMap(Carbon $date): array
    {
        $holidays = self::HOLIDAYS_STATIC;

        // dynamic holidays
        $easter = easter_date($date->year);
        $holidays[self::dateFromEaster($easter, -2)] = self::KARFREITAG;
        $holidays[self::dateFromEaster($easter, 0)] = self::OSTERSONNTAG;
        $holidays[self::dateFromEaster($easter, 1)] = self::OSTERMONTAG;
        $holidays[self::dateFromEaster($easter, 39)] = self::CHRISTI_HIMMELFAHRT;
        $holidays[self::dateFromEaster($easter, 49)] = self::PFINGSTSONNTAG;
        $holidays[self::dateFromEaster($easter, 50)] = self::PFINGSTMONTAG;
        $holidays[self::dateFromEaster($easter, 60)] = self::FRONLEICHNAM;

        // exceptional holidays
        if ('2017' === $date->format('Y')) {
            $holidays['3110'] = self::REFORMATIONSTAG_500_JAHRE_REFORMATION;
        }

        // Betriebsausflüge
        switch ($date->format('Y')) {
            case '2019':
                $holidays['2207'] = self::BETRIEBSAUSFLUG_2019;
                break;
        }

        /** @var array<string, string> $holidays */
        return $holidays;
    }

    protected static function dateFromEaster(int $easter, int $daysAway): string
    {
        $date = Carbon::createFromTimestamp($easter + ($daysAway * self::SECONDS_IN_A_DAY));

        return self::dayOfTheYear($date);
    }

    protected static function dayOfTheYear(Carbon $date): string
    {
        return $date->format('dm');
    }
}
