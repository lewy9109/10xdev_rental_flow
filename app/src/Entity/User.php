<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contract\RentalContract;
use App\Entity\Property\Property;
use App\Entity\PropertyUnit\PropertyUnit;
use App\Entity\Security\PasswordResetToken;
use App\Entity\Tenant\Tenant;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[ORM\UniqueConstraint(name: 'uniq_app_user_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $email;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $fullName;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Property>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Property::class)]
    private Collection $properties;

    /**
     * @var Collection<int, PropertyUnit>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: PropertyUnit::class)]
    private Collection $units;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Tenant::class)]
    private Collection $tenants;

    /**
     * @var Collection<int, RentalContract>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: RentalContract::class)]
    private Collection $contracts;

    /**
     * @var Collection<int, PasswordResetToken>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PasswordResetToken::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $passwordResetTokens;

    public function __construct(string $email, string $password, ?string $fullName = null)
    {
        $this->id = Uuid::v7();
        $this->email = mb_strtolower($email);
        $this->password = $password;
        $this->fullName = $fullName;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->properties = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->passwordResetTokens = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function updateEmail(string $email): void
    {
        $this->email = mb_strtolower($email);
        $this->touch();
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function updateFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
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

    public function markLogin(DateTimeImmutable $moment): void
    {
        $this->lastLoginAt = $moment;
        $this->touch();
    }

    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->lastLoginAt;
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
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = array_values($roles);
        $this->touch();
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
        $this->touch();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @return Collection<int, PropertyUnit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
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
