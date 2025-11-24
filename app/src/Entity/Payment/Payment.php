<?php

declare(strict_types=1);

namespace App\Entity\Payment;

use App\Entity\Contract\RentalContract;
use App\Entity\Installment\Installment;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: RentalContract::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private RentalContract $contract;

    #[ORM\ManyToOne(targetEntity: Installment::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Installment $installment;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $paymentDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $receivedAt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct(Installment $installment, string $amount, DateTimeImmutable $paymentDate)
    {
        $this->id = Uuid::v7();
        $this->installment = $installment;
        $this->contract = $installment->getContract();
        $this->owner = $this->contract->getOwner();
        $this->amount = $amount;
        $this->paymentDate = $paymentDate;
        $this->receivedAt = new DateTimeImmutable();
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

    public function getInstallment(): Installment
    {
        return $this->installment;
    }

    public function moveToInstallment(Installment $installment): void
    {
        if (!$installment->getContract()->getId()->equals($this->contract->getId())) {
            throw new InvalidArgumentException('Installment does not belong to the same contract.');
        }

        $this->installment = $installment;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function updateAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getPaymentDate(): DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function updatePaymentDate(DateTimeImmutable $date): void
    {
        $this->paymentDate = $date;
    }

    public function getReceivedAt(): DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function updateNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}
