<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Contract\RentalContract;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'tenants')]
#[ORM\UniqueConstraint(name: 'uniq_tenant_owner_email', columns: ['owner_id', 'email'])]
class Tenant
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tenants')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Embedded(class: TenantName::class)]
    private TenantName $name;

    #[ORM\Embedded(class: TenantEmail::class, columnPrefix: false)]
    private TenantEmail $email;

    #[ORM\Embedded(class: TenantPhone::class, columnPrefix: false)]
    private ?TenantPhone $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, RentalContract>
     */
    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: RentalContract::class)]
    private Collection $contracts;

    public function __construct(User $owner, TenantName $name, TenantEmail $email, ?TenantPhone $phone = null)
    {
        $this->id = Uuid::v7();
        $this->owner = $owner;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
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

    public function getName(): TenantName
    {
        return $this->name;
    }

    public function rename(TenantName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function getEmail(): TenantEmail
    {
        return $this->email;
    }

    public function updateEmail(TenantEmail $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function getPhone(): ?TenantPhone
    {
        return $this->phone;
    }

    public function updatePhone(?TenantPhone $phone): void
    {
        $this->phone = $phone;
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
