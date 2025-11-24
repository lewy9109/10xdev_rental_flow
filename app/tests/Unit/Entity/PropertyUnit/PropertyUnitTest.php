<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\PropertyUnit;

use App\Tests\Unit\Entity\EntityFactory;
use PHPUnit\Framework\TestCase;

final class PropertyUnitTest extends TestCase
{
    public function testUnitCopiesOwnerFromProperty(): void
    {
        $property = EntityFactory::property();
        $unit = EntityFactory::unit($property, 'B-2');

        self::assertSame($property->getOwner(), $unit->getOwner());
        self::assertTrue($unit->belongsTo($property));
    }

    public function testActivationFlagsToggle(): void
    {
        $unit = EntityFactory::unit();

        $unit->deactivate();
        self::assertFalse($unit->isActive());

        $unit->activate();
        self::assertTrue($unit->isActive());
    }
}
