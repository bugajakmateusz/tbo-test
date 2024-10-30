<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof \Tab\Infrastructure\Symfony\Security\UserInterface) {
            return;
        }

        $loggedUser = $user->loggedUser();
    }
}
