<?php

declare(strict_types=1);

namespace Tab\Application\Service\LoggedUser;

final class NobodyIsLoggedInException extends LoggedUserException
{
    public static function create(): self
    {
        return new self('Nobody is logged in.');
    }
}
