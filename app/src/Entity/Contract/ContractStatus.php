<?php

declare(strict_types=1);

namespace App\Entity\Contract;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case TERMINATED = 'terminated';
    case EXPIRED = 'expired';
}
