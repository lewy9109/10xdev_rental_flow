<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Security;

use App\Entity\Security\PasswordResetToken;
use App\Tests\Unit\Entity\EntityFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class PasswordResetTokenTest extends TestCase
{
    public function testTokenTracksUsage(): void
    {
        $user = EntityFactory::owner();
        $expiresAt = new DateTimeImmutable('+1 hour');
        $token = new PasswordResetToken($user, Uuid::v4(), $expiresAt);

        self::assertFalse($token->isExpiredAt(new DateTimeImmutable()));

        $moment = new DateTimeImmutable('+2 hours');
        self::assertTrue($token->isExpiredAt($moment));

        $token->markUsed();
        self::assertNotNull($token->getUsedAt());
    }
}
