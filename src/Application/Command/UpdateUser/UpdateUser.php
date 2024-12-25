<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateUser;

use Polsl\Domain\Email as DomainEmail;
use Polsl\Domain\Model\User\Name;
use Polsl\Domain\Model\User\Password;
use Polsl\Domain\Model\User\Role;
use Polsl\Infrastructure\Symfony\Validator\Constraints\EmailExists;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class UpdateUser
{
    /** @param array<string> $roles */
    public function __construct(
        public int $id,
        #[NotBlank(allowNull: true, normalizer: 'trim')]
        #[Length(max: Name::MAX_LENGTH)]
        public ?string $name,
        #[NotBlank(allowNull: true, normalizer: 'trim')]
        #[Length(max: Name::MAX_LENGTH)]
        public ?string $surname,
        #[NotBlank(allowNull: true, normalizer: 'trim')]
        #[Length(max: DomainEmail::MAX_LENGTH)]
        #[Email(mode: 'html5')]
        #[EmailExists(groups: ['changedEmail'])]
        public ?string $email,
        #[NotBlank(allowNull: true, normalizer: 'trim')]
        #[Length(min: Password::MIN_LENGTH, max: Password::MAX_LENGTH)]
        public ?string $password,
        #[NotBlank(allowNull: true, normalizer: 'trim')]
        #[All([
            new Choice(callback: [Role::class, 'list']),
        ])]
        public ?array $roles,
    ) {}
}
