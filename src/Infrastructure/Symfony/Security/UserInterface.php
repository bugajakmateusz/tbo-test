<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Symfony\Security;

use Polsl\Domain\Model\Login\LoggedUser;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function loggedUser(): LoggedUser;
}
