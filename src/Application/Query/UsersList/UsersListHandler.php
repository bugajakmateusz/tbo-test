<?php

declare(strict_types=1);

namespace Tab\Application\Query\UsersList;

use Tab\Application\Exception\ApplicationException;
use Tab\Application\Schema\UserSchema;
use Tab\Application\Service\LoggedUserServiceInterface;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filter;
use Tab\Packages\ResourcesList\Filters;

final readonly class UsersListHandler
{
    public const FILTER_ME = 'me';
    public const FILTER_ID = 'id';
    private const SUPPORTED_FILTERS = [self::FILTER_ME];

    public function __construct(
        private UsersListQueryInterface $usersListQuery,
        private LoggedUserServiceInterface $loggedUser,
    ) {
    }

    public function __invoke(UsersList $usersList): UsersListView
    {
        $filters = $usersList->filters;
        $filters->checkSupportedFilters(...self::SUPPORTED_FILTERS);
        $nonEmptyFilters = $this->checkMeFilter($filters);
        $fields = $this->resolveFields($filters, $usersList->fields);

        $users = $this->usersListQuery
            ->query(
                $usersList->page,
                $nonEmptyFilters,
                $fields,
            )
        ;

        return $users;
    }

    private function checkMeFilter(Filters &$filters): Filters
    {
        $nonEmptyFilters = Filters::fromFilters(...$filters->nonEmptyFilters());
        if (false === $nonEmptyFilters->has(self::FILTER_ME)) {
            return $nonEmptyFilters;
        }

        $meFilter = $nonEmptyFilters->get(self::FILTER_ME);
        if ('1' !== $meFilter->stringValue()) {
            throw new ApplicationException("'me' filter requires value '1'.");
        }
        $user = $this->loggedUser
            ->loggedUser()
        ;
        $idFilter = new Filter(self::FILTER_ID, $user->id());

        return Filters::fromFilters($idFilter, ...$nonEmptyFilters->toArray());
    }

    private function resolveFields(
        Filters $filters,
        ?Fields $overwriteFields = null,
    ): Fields {
        if (null !== $overwriteFields && false === $overwriteFields->isEmpty()) {
            return $overwriteFields;
        }

        $teacherFields = [
            UserSchema::ATTRIBUTE_NAME,
            UserSchema::ATTRIBUTE_SURNAME,
        ];

        if ($filters->has(self::FILTER_ME)) {
            $teacherFields[] = UserSchema::ATTRIBUTE_EMAIL;
            $teacherFields[] = UserSchema::ATTRIBUTE_ROLES;
        }

        return Fields::createFromArray(
            [
                UserSchema::TYPE => $teacherFields,
            ],
        );
    }
}
