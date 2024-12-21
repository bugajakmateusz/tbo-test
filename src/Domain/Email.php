<?php

declare(strict_types=1);

namespace Tab\Domain;

final class Email
{
    public const MAIL_REG_EX = '/^(([^<>()[\\]\\.,;:\\s@"]+(\\.[^<>()[\\]\\.,;:\\s@"]+)*)|(".+"))@(([^<>()[\\]\\.,;:\\s@"]+\\.)+[^<>()[\\]\\.,;:\\s@"]{2,})$/i';
    public const MAX_LENGTH = 250;

    private function __construct(private readonly string $email) {}

    public static function fromString(string $email): self
    {
        $normalizedEmail = \mb_convert_case($email, \MB_CASE_LOWER);
        $sanitizedEmail = SanitizedString::create($normalizedEmail);
        $sanitizedEmail->checkIsEmpty('E-mail');
        $sanitizedEmail->checkMaxLength(self::MAX_LENGTH, 'E-mail');
        $regExMatch = \preg_match(self::MAIL_REG_EX, $sanitizedEmail->toString());

        if (1 !== $regExMatch) {
            throw new DomainException("E-mail '{$email}' is not valid.");
        }

        return new self($sanitizedEmail->toString());
    }

    public function equalsTo(self $email): bool
    {
        return $this->email === $email->email;
    }

    public function toString(): string
    {
        return $this->email;
    }
}
