<?php

declare(strict_types=1);

namespace App\Entity\Property;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PropertyAddress
{
    #[ORM\Column(name: 'address_line1', length: 255)]
    private string $line1;

    #[ORM\Column(name: 'address_line2', length: 255, nullable: true)]
    private ?string $line2;

    #[ORM\Column(name: 'postal_code', length: 32, nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(name: 'city', length: 120, nullable: true)]
    private ?string $city;

    #[ORM\Column(name: 'country', length: 120, nullable: true)]
    private ?string $country;

    public function __construct(
        string $line1,
        ?string $line2 = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $country = null,
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
    }

    public function getLine1(): string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}
