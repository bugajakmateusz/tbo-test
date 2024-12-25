<?php

declare(strict_types=1);

namespace Polsl\Application\Service\LoggedUser;

final class UnsupportedUserInstanceException extends LoggedUserException
{
    public static function create(string $userClass, string $supportedClass): self
    {
        return new self("Current user class is '{$userClass}', but '{$supportedClass}' is expected.");
    }
}
