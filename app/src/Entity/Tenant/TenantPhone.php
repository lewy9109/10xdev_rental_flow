<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class TenantPhone
{
    #[ORM\Column(name: 'phone', length: 40, nullable: true)]
    private ?string $value;

    public function __construct(?string $value)
    {
        if ($value === null) {
            $this->value = null;

            return;
        }

        $normalized = preg_replace('/\s+/', '', $value);
        if ($normalized === '' || !ctype_digit($normalized)) {
            throw new InvalidArgumentException('Phone number must contain only digits.');
        }

        $this->value = $normalized;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null;
    }
}
