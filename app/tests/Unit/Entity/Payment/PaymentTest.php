<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Payment;

use App\Tests\Unit\Entity\EntityFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    public function testPaymentInheritsOwnerAndContractFromInstallment(): void
    {
        $installment = EntityFactory::installment();
        $payment = EntityFactory::payment($installment, '500.00');

        self::assertSame($installment->getContract(), $payment->getContract());
        self::assertSame('500.00', $payment->getAmount());
    }

    public function testMovingToInstallmentValidatesContract(): void
    {
        $installment = EntityFactory::installment();
        $otherInstallment = EntityFactory::installment(EntityFactory::contract());
        $payment = EntityFactory::payment($installment);

        $this->expectException(InvalidArgumentException::class);
        $payment->moveToInstallment($otherInstallment);
    }
}
