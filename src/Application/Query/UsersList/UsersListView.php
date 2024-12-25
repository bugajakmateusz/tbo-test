<?php

declare(strict_types=1);

namespace Polsl\Application\Query\UsersList;

use Polsl\Application\View\UserView;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\TotalItems;

final readonly class UsersListView
{
    /** @var UserView[] */
    public array $users;

    public function __construct(
        public TotalItems $totalItems,
        public Fields $fields,
        UserView ...$users,
    ) {
        $this->users = $users;
    }
}
