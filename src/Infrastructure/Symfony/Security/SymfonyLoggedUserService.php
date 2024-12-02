<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tab\Application\Service\LoggedUser\NobodyIsLoggedInException;
use Tab\Application\Service\LoggedUser\UnsupportedUserInstanceException;
use Tab\Application\Service\LoggedUserServiceInterface;

final class SymfonyLoggedUserService implements LoggedUserServiceInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function loggedUser(): \Tab\Domain\Model\Login\LoggedUser
    {
        $token = $this->tokenStorage
            ->getToken()
        ;
        $user = $token?->getUser();

        if (null === $user) {
            throw NobodyIsLoggedInException::create();
        }

        if (!$user instanceof UserInterface) {
            throw UnsupportedUserInstanceException::create($user::class, UserInterface::class);
        }

        return $user->loggedUser();
    }
}
