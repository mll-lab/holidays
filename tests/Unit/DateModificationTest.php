<?php

declare(strict_types=1);

namespace MLL\Holidays\Tests\Unit;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use MLL\Holidays\DateModification;
use PHPUnit\Framework\TestCase;

final class DateModificationTest extends TestCase
{
    public function testAddDays(): void
    {
        $sunday = self::sunday();
        $tuesdayAfter = self::sunday()->addDays(2);
        self::assertTrue(
            DateModification::addDays($sunday, 1, function (Carbon $date): bool {
                return $date->dayOfWeek !== CarbonInterface::MONDAY;
            })
                ->isSameDay($tuesdayAfter)
        );
    }

    protected static function sunday(): Carbon
    {
        return Carbon::createStrict(2019, 11, 3);
    }
}
