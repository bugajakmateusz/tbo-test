<?php

declare(strict_types=1);

namespace Polsl\Application\Service\LoggedUser;

final class NobodyIsLoggedInException extends LoggedUserException
{
    public static function create(): self
    {
        return new self('Nobody is logged in.');
    }
}
