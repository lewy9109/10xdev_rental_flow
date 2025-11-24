<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Contract\ContractStatus;
use App\Entity\Contract\RentalContract;
use App\Entity\Installment\Installment;
use App\Entity\Installment\InstallmentType;
use App\Entity\Payment\Payment;
use App\Entity\Property\Property;
use App\Entity\Property\PropertyAddress;
use App\Entity\Property\PropertyType;
use App\Entity\PropertyUnit\PropertyUnit;
use App\Entity\Tenant\Tenant;
use App\Entity\Tenant\TenantEmail;
use App\Entity\Tenant\TenantName;
use App\Entity\Tenant\TenantPhone;
use App\Entity\User;
use DateTimeImmutable;

final class EntityFactory
{
    public static function owner(string $email = 'owner@example.com'): User
    {
        return new User($email, 'hashed-password', 'Owner Name');
    }

    public static function property(?User $owner = null, string $label = 'Main Property'): Property
    {
        $owner ??= self::owner();

        return new Property(
            $owner,
            $label,
            new PropertyAddress('123 River Street', 'Unit 2B', '00-000', 'Springfield', 'US'),
            PropertyType::APARTMENT,
        );
    }

    public static function unit(?Property $property = null, string $label = 'A-1'): PropertyUnit
    {
        $property ??= self::property();

        return new PropertyUnit($property, $label);
    }

    public static function tenant(?User $owner = null, string $email = 'tenant@example.com'): Tenant
    {
        $owner ??= self::owner('tenant-owner@example.com');

        return new Tenant(
            $owner,
            new TenantName('Jane', 'Doe'),
            new TenantEmail($email),
            new TenantPhone('1234567890'),
        );
    }

    public static function contract(?User $owner = null): RentalContract
    {
        $owner ??= self::owner('contract-owner@example.com');
        $property = self::property($owner, 'Contract Property');
        $unit = new PropertyUnit($property, 'Unit-10');
        $tenant = new Tenant(
            $owner,
            new TenantName('John', 'Smith'),
            new TenantEmail('tenant+contract@example.com'),
            new TenantPhone('48600700800'),
        );

        return new RentalContract(
            $owner,
            $unit,
            $tenant,
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-12-31'),
            '1500.00',
            '1500.00',
            ContractStatus::ACTIVE,
        );
    }

    public static function installment(?RentalContract $contract = null, int $sequence = 1): Installment
    {
        $contract ??= self::contract();

        return new Installment(
            $contract,
            $sequence,
            InstallmentType::RENT,
            '1500.00',
            new DateTimeImmutable('2024-02-01'),
        );
    }

    public static function payment(?Installment $installment = null, string $amount = '1500.00'): Payment
    {
        $installment ??= self::installment();

        return new Payment($installment, $amount, new DateTimeImmutable('2024-02-05'));
    }
}
