<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewUser;

use Polsl\Domain\Email;
use Polsl\Domain\Model\User\Name;
use Polsl\Domain\Model\User\Password;
use Polsl\Domain\Model\User\Role;
use Polsl\Domain\Model\User\User;
use Polsl\Domain\Model\User\UserRepositoryInterface;
use Polsl\Packages\PasswordHasher\PasswordHasherInterface;

final readonly class AddNewUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(AddNewUser $command): void
    {
        $roleObjects = \array_map(
            static fn (string $role): Role => Role::from($role),
            $command->roles,
        );

        $user = User::register(
            Email::fromString($command->email),
            Password::hash($command->password, $this->passwordHasher),
            Name::fromString($command->name),
            Name::fromString($command->surname),
            $this->userRepository,
            ...$roleObjects,
        );

        $this->userRepository
            ->add($user)
        ;
    }
}
