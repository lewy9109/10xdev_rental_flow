<?php

declare(strict_types=1);

namespace App\Entity\PropertyUnit;

use App\Entity\Contract\RentalContract;
use App\Entity\Property\Property;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'property_units')]
#[ORM\UniqueConstraint(name: 'uniq_owner_property_unit_label', columns: ['owner_id', 'property_id', 'unit_label'])]
class PropertyUnit
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'units')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: Property::class, inversedBy: 'units')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private Property $property;

    #[ORM\Column(name: 'unit_label', length: 80)]
    private string $label;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $floorAreaSqm = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $bedrooms = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, RentalContract>
     */
    #[ORM\OneToMany(mappedBy: 'propertyUnit', targetEntity: RentalContract::class)]
    private Collection $contracts;

    public function __construct(Property $property, string $label)
    {
        $this->id = Uuid::v7();
        $this->property = $property;
        $this->owner = $property->getOwner();
        $this->label = $label;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->contracts = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function belongsTo(Property $property): bool
    {
        return $this->property->getId()->equals($property->getId());
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function rename(string $label): void
    {
        $this->label = $label;
        $this->touch();
    }

    public function getFloorAreaSqm(): ?float
    {
        return $this->floorAreaSqm;
    }

    public function updateFloorArea(?float $area): void
    {
        $this->floorAreaSqm = $area;
        $this->touch();
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function updateBedrooms(?int $bedrooms): void
    {
        $this->bedrooms = $bedrooms;
        $this->touch();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->touch();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->touch();
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function updateNotes(?string $notes): void
    {
        $this->notes = $notes;
        $this->touch();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, RentalContract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
