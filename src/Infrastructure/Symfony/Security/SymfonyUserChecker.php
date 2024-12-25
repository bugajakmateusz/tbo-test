<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonyUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void {}

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof \Polsl\Infrastructure\Symfony\Security\UserInterface) {
            return;
        }

        $loggedUser = $user->loggedUser();
    }
}
