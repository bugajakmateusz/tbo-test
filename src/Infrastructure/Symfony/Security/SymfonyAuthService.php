<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tab\Application\Exception\ApplicationException;
use Tab\Application\Service\AuthServiceInterface;
use Tab\Domain\Email;

final class SymfonyAuthService implements AuthServiceInterface
{
    private const FIREWALL_NAME = 'main';

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserProviderInterface $userProvider,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function login(Email $email): void
    {
        [
            'user' => $user,
            'token' => $userToken,
        ] = $this->setToken($email);
        /** @var Request $currentRequest */
        $currentRequest = $this->requestStack
            ->getCurrentRequest()
        ;
        \dump('InteractiveLoginEvent');
        $event = new InteractiveLoginEvent($currentRequest, $userToken);
        $this->eventDispatcher
            ->dispatch($event)
        ;
    }

    /**
     * @return array{
     *     user: UserInterface,
     *     token: AbstractToken,
     * }
     *
     * @throws ApplicationException
     */
    private function setToken(Email $email): array
    {
        $user = $this->userProvider
            ->loadUserByIdentifier($email->toString())
        ;

        if (!$user instanceof UserInterface) {
            $expectedClass = UserInterface::class;
            $actualClass = $user::class;

            throw new ApplicationException("Expected '{$expectedClass}' instance, got '{$actualClass}'.");
        }

        $userToken = new UsernamePasswordToken(
            $user,
            self::FIREWALL_NAME,
            $user->getRoles(),
        );
        $this->tokenStorage
            ->setToken($userToken)
        ;

        return [
            'user' => $user,
            'token' => $userToken,
        ];
    }
}
