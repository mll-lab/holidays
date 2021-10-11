<?php

declare(strict_types=1);

namespace MLL\Holidays;

use Carbon\Carbon;

class BavarianHolidays
{
    public const HOLIDAYS_STATIC = [
        '01.01' => 'Neujahrstag',
        '06.01' => 'Heilige Drei Könige',
        '01.05' => 'Tag der Arbeit',
        '15.08' => 'Maria Himmelfahrt',
        '03.10' => 'Tag der Deutschen Einheit',
        '01.11' => 'Allerheiligen',
        '24.12' => 'Heilig Abend',
        '25.12' => 'Erster Weihnachtstag',
        '26.12' => 'Zweiter Weihnachtstag',
        '31.12' => 'Sylvester',
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

    /**
     * Optionally allows users to define extra holidays for a given year.
     *
     * The returned array is expected to be a map from the day of the year
     * (format with @see self::dayOfTheYear()) to holiday names.
     *
     * @example ['23.02' => 'Day of the Tentacle']
     *
     * @var (callable(int $year): array<string, string>)|null
     */
    public static $loadUserDefinedHolidays;

    /**
     * Checks if given date is a working day.
     */
    public static function isWorkingDay(Carbon $date): bool
    {
        return is_null(self::name($date));
    }

    /**
     * Checks if given date is a holiday.
     */
    public static function isHoliday(Carbon $date): bool
    {
        return ! self::isWorkingDay($date);
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

    public static function addWorkingDays(Carbon $date, int $days): Carbon
    {
        // Make sure we do not mutate the original date
        $copy = $date->clone();

        while ($days > 0) {
            $copy->addDay();
            if (self::isWorkingDay($copy)) {
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
        if (2017 === $date->year) {
            $holidays['31.10'] = self::REFORMATIONSTAG_500_JAHRE_REFORMATION;
        }

        // user-defined holidays
        if (isset(self::$loadUserDefinedHolidays)) {
            $holidays = array_merge(
                $holidays,
                (self::$loadUserDefinedHolidays)($date->year)
            );
        }

        return $holidays;
    }

    protected static function dateFromEaster(int $easter, int $daysAway): string
    {
        $date = Carbon::createFromTimestamp($easter)
            ->setTimezone('Europe/Berlin')
            ->addDays($daysAway);

        return self::dayOfTheYear($date);
    }

    public static function dayOfTheYear(Carbon $date): string
    {
        return $date->format('d.m');
    }
}
