<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TenantName
{
    #[ORM\Column(name: 'first_name', length: 120)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', length: 120)]
    private string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function first(): string
    {
        return $this->firstName;
    }

    public function last(): string
    {
        return $this->lastName;
    }

    public function full(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }
}
