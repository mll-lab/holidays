<?php

declare(strict_types=1);

namespace MLL\Holidays\Tests\Unit;

use Carbon\Carbon;
use MLL\Holidays\Holidays;
use PHPUnit\Framework\TestCase;

final class HolidaysTest extends TestCase
{
    public function testName(): void
    {
        self::assertNull(Holidays::name(self::workingDayWednesday()));
        self::assertSame(Holidays::SONNTAG, Holidays::name(self::sunday()));
        var_dump(self::easter2019());
        self::assertSame(Holidays::OSTERSONNTAG, Holidays::name(self::easter2019()));
    }

    /**
     * @dataProvider workingDays
     */
    public function testWorkingDays(Carbon $workingDay): void
    {
        self::assertTrue(Holidays::isMLLWorkingDay($workingDay));
        self::assertFalse(Holidays::isMLLHoliday($workingDay));
    }

    /**
     * @dataProvider holidays
     */
    public function testHolidays(Carbon $holiday): void
    {
        self::assertFalse(Holidays::isMLLWorkingDay($holiday));
        self::assertTrue(Holidays::isMLLHoliday($holiday));
    }

    public function testAddMllWorkingDays(): void
    {
        $saturday = self::saturday();
        $mondayAfter = self::saturday()->addDays(2);

        self::assertTrue(
            Holidays::addMLLWorkingDays($saturday, 1)
                ->isSameDay($mondayAfter),
            'Skips over sunday'
        );
        self::assertTrue(
            $saturday->isSameDay(self::saturday()),
            'Should not mutate the original date'
        );
    }

    protected static function workingDayWednesday(): Carbon
    {
        return Carbon::createStrict(2019, 10, 30);
    }

    protected static function easter2019(): Carbon
    {
        return Carbon::createStrict(2019, 4, 21);
    }

    protected static function saturday(): Carbon
    {
        return Carbon::createStrict(2019, 11, 2);
    }

    protected static function sunday(): Carbon
    {
        return Carbon::createStrict(2019, 11, 3);
    }

    /**
     * @return iterable<int, array{Carbon}>
     */
    public static function workingDays(): iterable
    {
        yield [self::workingDayWednesday()];
    }

    /**
     * @return iterable<int, array{Carbon}>
     */
    public static function holidays(): iterable
    {
        yield [self::easter2019()];
        yield [self::easter2019()];
    }
}
