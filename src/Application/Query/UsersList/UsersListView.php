<?php

declare(strict_types=1);

namespace Tab\Application\Query\UsersList;

use Tab\Application\View\UserView;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\TotalItems;

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
