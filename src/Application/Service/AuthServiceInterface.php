<?php

declare(strict_types=1);

namespace Tab\Application\Service;

use Tab\Domain\Email;

interface AuthServiceInterface
{
    /**
     * This method replaces security token manually, should be used with care
     * and only in special cases like account confirmation.
     */
    public function login(Email $email): void;
}
