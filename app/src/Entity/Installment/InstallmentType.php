<?php

declare(strict_types=1);

namespace App\Entity\Installment;

enum InstallmentType: string
{
    case RENT = 'rent';
    case DEPOSIT = 'deposit';
    case ADJUSTMENT = 'adjustment';
}
