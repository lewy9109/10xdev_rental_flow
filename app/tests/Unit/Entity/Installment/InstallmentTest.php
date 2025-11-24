<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Installment;

use App\Entity\Installment\Installment;
use App\Entity\Installment\InstallmentType;
use App\Tests\Unit\Entity\EntityFactory;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InstallmentTest extends TestCase
{
    public function testManualOverrideIsTracked(): void
    {
        $contract = EntityFactory::contract();
        $installment = new Installment($contract, 1, InstallmentType::RENT, '1500.00', new DateTimeImmutable('2024-02-01'));

        $installment->updateAmount('1750.00', true);
        $installment->updateDueDate(new DateTimeImmutable('2024-02-05'));

        self::assertTrue($installment->isManualOverride());
        self::assertSame('1750.00', $installment->getAmount());
        self::assertEquals('2024-02-05', $installment->getDueDate()->format('Y-m-d'));
    }

    public function testSequenceMustBePositive(): void
    {
        $contract = EntityFactory::contract();

        $this->expectException(InvalidArgumentException::class);
        new Installment($contract, 0, InstallmentType::RENT, '1500.00', new DateTimeImmutable('2024-02-01'));
    }
}
