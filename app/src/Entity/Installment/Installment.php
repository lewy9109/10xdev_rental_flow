<?php

declare(strict_types=1);

namespace App\Entity\Installment;

use App\Entity\Contract\RentalContract;
use App\Entity\Payment\Payment;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'installments')]
#[ORM\UniqueConstraint(name: 'uniq_installment_sequence', columns: ['contract_id', 'sequence_no'])]
class Installment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: RentalContract::class, inversedBy: 'installments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private RentalContract $contract;

    #[ORM\Column(name: 'sequence_no', type: 'integer')]
    private int $sequenceNo;

    #[ORM\Column(enumType: InstallmentType::class)]
    private InstallmentType $type;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $dueDate;

    #[ORM\Column(type: 'boolean')]
    private bool $manualOverride = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(mappedBy: 'installment', targetEntity: Payment::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $payments;

    public function __construct(
        RentalContract $contract,
        int $sequenceNo,
        InstallmentType $type,
        string $amount,
        DateTimeImmutable $dueDate,
    ) {
        if ($sequenceNo < 1) {
            throw new InvalidArgumentException('Sequence number must be positive.');
        }

        $this->id = Uuid::v7();
        $this->contract = $contract;
        $this->owner = $contract->getOwner();
        $this->sequenceNo = $sequenceNo;
        $this->type = $type;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
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

    public function getContract(): RentalContract
    {
        return $this->contract;
    }

    public function getSequenceNo(): int
    {
        return $this->sequenceNo;
    }

    public function getType(): InstallmentType
    {
        return $this->type;
    }

    public function updateType(InstallmentType $type): void
    {
        $this->type = $type;
        $this->touch();
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function updateAmount(string $amount, bool $manualOverride = false): void
    {
        $this->amount = $amount;
        $this->manualOverride = $manualOverride || $this->manualOverride;
        $this->touch();
    }

    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function updateDueDate(DateTimeImmutable $dueDate, bool $manualOverride = false): void
    {
        $this->dueDate = $dueDate;
        $this->manualOverride = $manualOverride || $this->manualOverride;
        $this->touch();
    }

    public function isManualOverride(): bool
    {
        return $this->manualOverride;
    }

    public function flagManualOverride(): void
    {
        $this->manualOverride = true;
        $this->touch();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function updateDescription(?string $description): void
    {
        $this->description = $description;
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

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
