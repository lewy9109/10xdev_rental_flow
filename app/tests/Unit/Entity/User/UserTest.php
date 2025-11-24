<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\User;

use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testUserInitializesWithDefaults(): void
    {
        $user = new User('Owner@Example.COM', 'hash', 'Rental Owner');

        self::assertSame('owner@example.com', $user->getEmail());
        self::assertSame('Rental Owner', $user->getFullName());
        self::assertTrue($user->isActive());
        self::assertNotNull($user->getCreatedAt());
        self::assertNotNull($user->getUpdatedAt());
    }

    public function testRolesAlwaysContainUser(): void
    {
        $user = new User('owner@example.com', 'hash');
        $user->setRoles(['ROLE_ADMIN']);

        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testMarkLoginUpdatesTimestamp(): void
    {
        $user = new User('owner@example.com', 'hash');
        $moment = new DateTimeImmutable('+1 day');

        $user->markLogin($moment);

        self::assertSame($moment, $user->getLastLoginAt());
        self::assertGreaterThanOrEqual($moment->getTimestamp(), $user->getUpdatedAt()->getTimestamp());
    }
}
