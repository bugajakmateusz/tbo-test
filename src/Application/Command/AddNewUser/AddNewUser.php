<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewUser;

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

final readonly class AddNewUser
{
    /** @param array<string> $roles */
    public function __construct(
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: DomainEmail::MAX_LENGTH)]
        #[Email(mode: 'html5')]
        #[EmailExists]
        public string $email,
        #[NotBlank(normalizer: 'trim')]
        #[Length(min: Password::MIN_LENGTH, max: Password::MAX_LENGTH)]
        public string $password,
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: Name::MAX_LENGTH)]
        public string $name,
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: Name::MAX_LENGTH)]
        public string $surname,
        #[NotBlank(normalizer: 'trim')]
        #[All([
            new Choice(callback: [Role::class, 'list']),
        ])]
        public array $roles = [],
    ) {}
}
