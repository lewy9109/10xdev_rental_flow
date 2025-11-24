<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Property;

use App\Entity\Property\Property;
use App\Entity\Property\PropertyAddress;
use App\Entity\Property\PropertyType;
use App\Entity\PropertyUnit\PropertyUnit;
use App\Tests\Unit\Entity\EntityFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PropertyTest extends TestCase
{
    public function testPropertyAcceptsUnitsBelongingToSameOwner(): void
    {
        $owner = EntityFactory::owner();
        $property = new Property($owner, 'Riverside Loft', new PropertyAddress('River 1'), PropertyType::HOUSE);
        $unit = new PropertyUnit($property, 'A-12');

        $property->addUnit($unit);

        self::assertCount(1, $property->getUnits());
        self::assertSame($unit, $property->getUnits()->first());
    }

    public function testUpdatingSizeValidatesValue(): void
    {
        $property = EntityFactory::property();

        $property->updateSize(72.5);
        self::assertSame(72.5, $property->getSizeSqm());

        $this->expectException(InvalidArgumentException::class);
        $property->updateSize(-10);
    }
}
