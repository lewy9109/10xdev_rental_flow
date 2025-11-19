<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function testGetterAndSetter(): void
    {
        $property = new Property();

        self::assertNull($property->getId());

        $property
            ->setName('Riverside Loft')
            ->setAddress('123 River Street, Springfield')
            ->setApartmentNumber('A-12')
            ->setSize(72.5)
            ->setType('Loft');

        self::assertSame('Riverside Loft', $property->getName());
        self::assertSame('123 River Street, Springfield', $property->getAddress());
        self::assertSame('A-12', $property->getApartmentNumber());
        self::assertSame(72.5, $property->getSize());
        self::assertSame('Loft', $property->getType());
    }
}
