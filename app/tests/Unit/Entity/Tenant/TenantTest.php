<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Tenant;

use App\Entity\Tenant\Tenant;
use App\Entity\Tenant\TenantEmail;
use App\Entity\Tenant\TenantName;
use App\Entity\Tenant\TenantPhone;
use App\Tests\Unit\Entity\EntityFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TenantTest extends TestCase
{
    public function testTenantStoresEmbeddedName(): void
    {
        $owner = EntityFactory::owner();
        $tenant = new Tenant(
            $owner,
            new TenantName('Anna', 'Nowak'),
            new TenantEmail('anna@example.com'),
            new TenantPhone('48600111222'),
        );

        self::assertSame('Anna', $tenant->getName()->first());
        self::assertSame('Nowak', $tenant->getName()->last());
        self::assertSame('anna@example.com', $tenant->getEmail()->value());
        self::assertSame('48600111222', $tenant->getPhone()?->value());
    }

    public function testInvalidEmailThrowsException(): void
    {
        $owner = EntityFactory::owner();

        $this->expectException(InvalidArgumentException::class);
        new Tenant($owner, new TenantName('Invalid', 'Email'), new TenantEmail('not-email'));
    }

    public function testInvalidPhoneThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TenantPhone('abc123');
    }
}
