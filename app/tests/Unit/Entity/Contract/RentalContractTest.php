<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Contract;

use App\Entity\Contract\ContractStatus;
use App\Entity\Contract\RentalContract;
use App\Entity\PropertyUnit\PropertyUnit;
use App\Entity\Tenant\Tenant;
use App\Entity\Tenant\TenantEmail;
use App\Entity\Tenant\TenantName;
use App\Tests\Unit\Entity\EntityFactory;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RentalContractTest extends TestCase
{
    public function testConstructorValidatesOwnership(): void
    {
        $owner = EntityFactory::owner();
        $property = EntityFactory::property($owner);
        $unit = new PropertyUnit($property, 'C-1');
        $foreignTenantOwner = EntityFactory::owner('other@example.com');
        $tenant = new Tenant(
            $foreignTenantOwner,
            new TenantName('Kate', 'Brown'),
            new TenantEmail('kate@example.com'),
        );

        $this->expectException(InvalidArgumentException::class);
        new RentalContract(
            $owner,
            $unit,
            $tenant,
            new DateTimeImmutable('2024-01-01'),
            null,
            '1200.00',
        );
    }

    public function testStatusAndBillingDayCanChange(): void
    {
        $contract = EntityFactory::contract();

        $contract->changeStatus(ContractStatus::TERMINATED);
        $contract->setBillingDay(10);
        $contract->updateEndDate(new DateTimeImmutable('2024-12-31'));

        self::assertSame(ContractStatus::TERMINATED, $contract->getStatus());
        self::assertSame(10, $contract->getBillingDay());
        self::assertEquals('2024-12-31', $contract->getEndDate()?->format('Y-m-d'));
    }
}
