<?php

declare(strict_types=1);

namespace App\Entity\Property;

use App\Entity\PropertyUnit\PropertyUnit;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'properties')]
class Property
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(length: 180)]
    private string $label;

    #[ORM\Embedded(class: PropertyAddress::class)]
    private PropertyAddress $address;

    #[ORM\Column(enumType: PropertyType::class)]
    private PropertyType $type;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sizeSqm = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, PropertyUnit>
     */
    #[ORM\OneToMany(mappedBy: 'property', targetEntity: PropertyUnit::class)]
    private Collection $units;

    public function __construct(
        User $owner,
        string $label,
        PropertyAddress $address,
        PropertyType $type = PropertyType::APARTMENT,
    ) {
        $this->id = Uuid::v7();
        $this->owner = $owner;
        $this->label = $label;
        $this->address = $address;
        $this->type = $type;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->units = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
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

    public function getAddress(): PropertyAddress
    {
        return $this->address;
    }

    public function updateAddress(PropertyAddress $address): void
    {
        $this->address = $address;
        $this->touch();
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }

    public function changeType(PropertyType $type): void
    {
        $this->type = $type;
        $this->touch();
    }

    public function getSizeSqm(): ?float
    {
        return $this->sizeSqm;
    }

    public function updateSize(?float $sizeSqm): void
    {
        if ($sizeSqm !== null && $sizeSqm < 0) {
            throw new InvalidArgumentException('Size must be positive.');
        }

        $this->sizeSqm = $sizeSqm;
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
     * @return Collection<int, PropertyUnit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(PropertyUnit $unit): void
    {
        if (!$unit->belongsTo($this)) {
            throw new InvalidArgumentException('Unit belongs to a different property.');
        }

        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
        }

        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
