<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Security;

use Polsl\Application\Service\LoggedUser\NobodyIsLoggedInException;
use Polsl\Application\Service\LoggedUser\UnsupportedUserInstanceException;
use Polsl\Application\Service\LoggedUserServiceInterface;
use Polsl\Domain\Model\Login\LoggedUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SymfonyLoggedUserService implements LoggedUserServiceInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage) {}

    public function loggedUser(): LoggedUser
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
