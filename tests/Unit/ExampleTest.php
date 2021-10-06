<?php

declare(strict_types=1);

namespace MLL\Holidays\Tests\Unit;

use PHPUnit\Framework;
use MLL\Holidays\Example;

class ExampleTest extends Framework\TestCase
{
    public function testGreetIncludesName(): void
    {
        $name = 'mll-lab';
        $example = new Example($name);

        self::assertStringContainsString($name, $example->greet());
    }
}
