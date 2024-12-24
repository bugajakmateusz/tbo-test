<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateUser;

use Tab\Domain\Email;
use Tab\Domain\Model\User\Name;
use Tab\Domain\Model\User\Password;
use Tab\Domain\Model\User\Role;
use Tab\Domain\Model\User\User;
use Tab\Domain\Model\User\UserRepositoryInterface;
use Tab\Packages\PasswordHasher\PasswordHasherInterface;

final readonly class UpdateUserHandler
{
    private const FIELD_NAME = 'name';
    private const FIELD_SURNAME = 'surname';
    private const FIELD_EMAIL = 'email';
    private const FIELD_PASSWORD = 'password';
    private const FIELD_ROLES = 'roles';

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(UpdateUser $command): void
    {
        $changes = $this->commandChangesToArray($command);
        $nonEmptyChanges = \array_filter(
            $changes,
            static fn (mixed $value): bool => null !== $value,
        );
        if ([] === $nonEmptyChanges) {
            return;
        }
        $user = $this->userRepository
            ->get($command->id)
        ;
        foreach ($nonEmptyChanges as $field => $value) {
            match ($field) {
                self::FIELD_NAME => $this->changeName($user, $value),
                self::FIELD_SURNAME => $this->changeSurname($user, $value),
                self::FIELD_EMAIL => $this->changeEmail($user, $value),
                self::FIELD_PASSWORD => $this->changePassword($user, $value),
                self::FIELD_ROLES => $this->changeRoles($user, $value),
            };
        }
    }

    /**
     * @return array{
     *     name: string|null,
     *     surname: string|null,
     *     email: string|null,
     *     password: string|null,
     *     roles: string[]|null,
     * }
     */
    private function commandChangesToArray(UpdateUser $command): array
    {
        return [
            self::FIELD_NAME => $command->name,
            self::FIELD_SURNAME => $command->surname,
            self::FIELD_EMAIL => $command->email,
            self::FIELD_PASSWORD => $command->password,
            self::FIELD_ROLES => $command->roles,
        ];
    }

    private function changeEmail(
        User $user,
        string $email,
    ): void {
        $newEmail = Email::fromString($email);
        $user->changeEmail($newEmail, $this->userRepository);
    }

    private function changePassword(
        User $user,
        string $password,
    ): void {
        $newPassword = Password::hash(
            $password,
            $this->passwordHasher,
        );
        $user->changePassword($newPassword);
    }

    /** @param array<string> $roles */
    private function changeRoles(
        User $user,
        array $roles,
    ): void {
        $roleObjects = \array_map(
            static fn (string $role): Role => Role::from($role),
            $roles,
        );
        $user->changeRoles(...$roleObjects);
    }

    private function changeName(
        User $user,
        string $name,
    ): void {
        $newName = Name::fromString($name);
        $user->changeName($newName);
    }

    private function changeSurname(
        User $user,
        string $surname,
    ): void {
        $newName = Name::fromString($surname);
        $user->changeSurname($newName);
    }
}
