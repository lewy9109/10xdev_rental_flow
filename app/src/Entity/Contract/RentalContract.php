<?php

declare(strict_types=1);

namespace App\Entity\Contract;

use App\Entity\Installment\Installment;
use App\Entity\Payment\Payment;
use App\Entity\PropertyUnit\PropertyUnit;
use App\Entity\Tenant\Tenant;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'rental_contracts')]
class RentalContract
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: PropertyUnit::class, inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private PropertyUnit $propertyUnit;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private Tenant $tenant;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $endDate;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $monthlyRentAmount;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $depositAmount;

    #[ORM\Column(enumType: ContractStatus::class)]
    private ContractStatus $status;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $billingDay = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Installment>
     */
    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: Installment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $installments;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct(
        User $owner,
        PropertyUnit $propertyUnit,
        Tenant $tenant,
        DateTimeImmutable $startDate,
        ?DateTimeImmutable $endDate,
        string $monthlyRentAmount,
        ?string $depositAmount = null,
        ContractStatus $status = ContractStatus::DRAFT,
    ) {
        $this->assertSameOwner($owner, $propertyUnit->getOwner(), 'Property unit');
        $this->assertSameOwner($owner, $tenant->getOwner(), 'Tenant');

        $this->id = Uuid::v7();
        $this->owner = $owner;
        $this->propertyUnit = $propertyUnit;
        $this->tenant = $tenant;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->monthlyRentAmount = $monthlyRentAmount;
        $this->depositAmount = $depositAmount;
        $this->status = $status;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->installments = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getPropertyUnit(): PropertyUnit
    {
        return $this->propertyUnit;
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function updateEndDate(?DateTimeImmutable $endDate): void
    {
        if ($endDate !== null && $endDate <= $this->startDate) {
            throw new InvalidArgumentException('End date must be later than start date.');
        }

        $this->endDate = $endDate;
        $this->touch();
    }

    public function getMonthlyRentAmount(): string
    {
        return $this->monthlyRentAmount;
    }

    public function updateMonthlyRentAmount(string $amount): void
    {
        $this->monthlyRentAmount = $amount;
        $this->touch();
    }

    public function getDepositAmount(): ?string
    {
        return $this->depositAmount;
    }

    public function updateDepositAmount(?string $amount): void
    {
        $this->depositAmount = $amount;
        $this->touch();
    }

    public function getStatus(): ContractStatus
    {
        return $this->status;
    }

    public function changeStatus(ContractStatus $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    public function getBillingDay(): ?int
    {
        return $this->billingDay;
    }

    public function setBillingDay(?int $billingDay): void
    {
        if ($billingDay !== null && ($billingDay < 1 || $billingDay > 28)) {
            throw new InvalidArgumentException('Billing day must be between 1 and 28.');
        }

        $this->billingDay = $billingDay;
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
     * @return Collection<int, Installment>
     */
    public function getInstallments(): Collection
    {
        return $this->installments;
    }

    public function addInstallment(Installment $installment): void
    {
        if (!$this->installments->contains($installment)) {
            $this->installments->add($installment);
        }

        $this->touch();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): void
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
        }

        $this->touch();
    }

    private function assertSameOwner(User $contractOwner, User $relatedOwner, string $context): void
    {
        if (!$contractOwner->getId()->equals($relatedOwner->getId())) {
            throw new InvalidArgumentException(sprintf('%s belongs to a different owner.', $context));
        }
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
